"""
Convert the project's SQLite database into a MySQL/MariaDB-compatible .sql dump.

Usage:
    python database/sqlite_to_mysql.py

Reads:  database/database.sqlite
Writes: database/treasury-management.sql

The output is safe to import into a fresh MySQL/MariaDB database via phpMyAdmin
(Hostinger) or `mysql -u user -p dbname < treasury-management.sql`.
"""
import os
import re
import sqlite3
import datetime

BASE = os.path.dirname(os.path.abspath(__file__))
SRC = os.path.join(BASE, "database.sqlite")
OUT = os.path.join(BASE, "treasury-management.sql")

# Tables that are internal to SQLite / Laravel framework state, skip schema+data
SKIP_TABLES = {"sqlite_sequence"}
# Tables whose *data* we don't want to carry to production (runtime/cache state)
TRUNCATE_DATA = {"cache", "cache_locks", "sessions", "jobs", "job_batches", "failed_jobs"}


def map_type(decl: str) -> str:
    """Map an SQLite declared type to a MySQL column type."""
    d = (decl or "").strip().lower()
    if d.startswith("tinyint"):
        return "TINYINT(1)"
    if "int" in d:
        return "BIGINT"
    if d.startswith("varchar"):
        return "VARCHAR(255)"
    if d in ("text", "clob") or "text" in d:
        return "TEXT"
    if d == "datetime" or d == "timestamp":
        return "DATETIME"
    if d == "date":
        return "DATE"
    if d == "time":
        return "TIME"
    if d in ("numeric", "decimal") or "decimal" in d or "numeric" in d:
        return "DECIMAL(15,2)"
    if "real" in d or "float" in d or "double" in d:
        return "DOUBLE"
    if "blob" in d:
        return "LONGBLOB"
    return "VARCHAR(255)"


def quote_default(val: str, mysql_type: str) -> str:
    v = val.strip()
    if v.upper() == "CURRENT_TIMESTAMP":
        return "CURRENT_TIMESTAMP"
    # strip surrounding quotes if present
    if (v.startswith("'") and v.endswith("'")) or (v.startswith('"') and v.endswith('"')):
        inner = v[1:-1]
        return "'" + inner.replace("'", "''") + "'"
    if v.upper() in ("NULL",):
        return "NULL"
    # numeric / boolean literal
    return "'" + v.replace("'", "''") + "'"


def esc(val) -> str:
    if val is None:
        return "NULL"
    if isinstance(val, (int, float)):
        return str(val)
    if isinstance(val, bytes):
        return "0x" + val.hex()
    if isinstance(val, (datetime.date, datetime.datetime)):
        return "'" + str(val) + "'"
    s = str(val)
    s = s.replace("\\", "\\\\").replace("'", "\\'").replace("\n", "\\n").replace("\r", "\\r").replace("\x00", "")
    return "'" + s + "'"


def main():
    con = sqlite3.connect(SRC)
    con.row_factory = sqlite3.Row
    cur = con.cursor()

    cur.execute(
        "SELECT name, sql FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%' ORDER BY name"
    )
    tables = [(r["name"], r["sql"]) for r in cur.fetchall() if r["name"] not in SKIP_TABLES]

    lines = []
    lines.append("-- MySQL / MariaDB dump generated from SQLite")
    lines.append("-- Source: database/database.sqlite")
    lines.append(f"-- Generated: {datetime.datetime.now().isoformat(timespec='seconds')}")
    lines.append("")
    lines.append("SET NAMES utf8mb4;")
    lines.append("SET FOREIGN_KEY_CHECKS = 0;")
    lines.append("SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';")
    lines.append("")

    fk_clauses_per_table = {}

    for name, create_sql in tables:
        # --- columns ---
        cur.execute(f'PRAGMA table_info("{name}")')
        cols = cur.fetchall()
        pk_cols = [c["name"] for c in cols if c["pk"]]

        # Detect single-integer-PK autoincrement (rowid alias)
        autoinc = (
            len(pk_cols) == 1
            and "int" in (next(c["type"] for c in cols if c["name"] == pk_cols[0]).lower())
            and "autoincrement" in (create_sql or "").lower()
        )

        col_defs = []
        for c in cols:
            cname = c["name"]
            mtype = map_type(c["type"])
            piece = f"  `{cname}` {mtype}"
            if autoinc and cname == pk_cols[0]:
                piece += " NOT NULL AUTO_INCREMENT"
            else:
                if c["notnull"]:
                    piece += " NOT NULL"
                else:
                    piece += " NULL"
                if c["dflt_value"] is not None:
                    piece += " DEFAULT " + quote_default(str(c["dflt_value"]), mtype)
            col_defs.append(piece)

        if pk_cols:
            col_defs.append("  PRIMARY KEY (" + ", ".join(f"`{p}`" for p in pk_cols) + ")")

        # --- indexes (unique + normal), excluding the auto PK / autoindex ---
        cur.execute(f'PRAGMA index_list("{name}")')
        for idx in cur.fetchall():
            iname = idx["name"]
            if iname.startswith("sqlite_autoindex"):
                continue
            cur.execute(f'PRAGMA index_info("{iname}")')
            icols = [ic["name"] for ic in cur.fetchall()]
            if not icols:
                continue
            cols_sql = ", ".join(f"`{c}`" for c in icols)
            if idx["unique"]:
                col_defs.append(f"  UNIQUE KEY `{iname}` ({cols_sql})")
            else:
                col_defs.append(f"  KEY `{iname}` ({cols_sql})")

        # --- foreign keys (added via ALTER after all tables exist) ---
        cur.execute(f'PRAGMA foreign_key_list("{name}")')
        fks = cur.fetchall()
        fk_clauses = []
        for fk in fks:
            on_del = (fk["on_delete"] or "NO ACTION").upper()
            fk_clauses.append(
                f"ALTER TABLE `{name}` ADD CONSTRAINT `{name}_{fk['from']}_foreign` "
                f"FOREIGN KEY (`{fk['from']}`) REFERENCES `{fk['table']}` (`{fk['to']}`) "
                f"ON DELETE {on_del};"
            )
        if fk_clauses:
            fk_clauses_per_table[name] = fk_clauses

        lines.append(f"DROP TABLE IF EXISTS `{name}`;")
        lines.append(f"CREATE TABLE `{name}` (")
        lines.append(",\n".join(col_defs))
        lines.append(") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;")
        lines.append("")

        # --- data ---
        if name in TRUNCATE_DATA:
            lines.append(f"-- (runtime table `{name}` left empty)")
            lines.append("")
            continue

        colnames = [c["name"] for c in cols]
        cur.execute(f'SELECT * FROM "{name}"')
        rows = cur.fetchall()
        if rows:
            collist = ", ".join(f"`{c}`" for c in colnames)
            for i in range(0, len(rows), 200):
                chunk = rows[i:i + 200]
                values = ",\n".join(
                    "(" + ", ".join(esc(r[c]) for c in colnames) + ")" for r in chunk
                )
                lines.append(f"INSERT INTO `{name}` ({collist}) VALUES")
                lines.append(values + ";")
            lines.append("")

    # foreign keys last
    lines.append("-- Foreign keys")
    for name in fk_clauses_per_table:
        for clause in fk_clauses_per_table[name]:
            lines.append(clause)
    lines.append("")
    lines.append("SET FOREIGN_KEY_CHECKS = 1;")
    lines.append("")

    with open(OUT, "w", encoding="utf-8") as f:
        f.write("\n".join(lines))

    con.close()
    print(f"Wrote {OUT} ({os.path.getsize(OUT)} bytes), {len(tables)} tables")


if __name__ == "__main__":
    main()
