# Instructions:
1. Always match the table layout to COLLECTIONS MANAGEMENT data table. **!IMPORTANT-STRICTLY FOLLOW**
2. Buttons must match the design ADD ENTRY button in OR RPT. **!IMPORTANT-STRICTLY FOLLOW**
3. You are able to freely design this module as long as all fields and flow from figma is not changed.
4. You must base your design using the skill UI/UX Pro max.
5. You can match the color scheme with the OR RPT Module.**!IMPORTANT-STRICTLY FOLLOW**
6. Maintain Figma Fonts used, except for buttons, we must changed it to Manrope. **!IMPORTANT-STRICTLY FOLLOW**
7. Input Fields must have the same layout with the OR RPT. **!IMPORTANT-STRICTLY FOLLOW**
8. Always create a new MD File if new module is created.
	- for example if we are going to create module 5 a module-5.md must be created and all changes and updates for module 5 must be put here.
9. **Check output for layout overlaps.** **!IMPORTANT-STRICTLY FOLLOW**
10. If I say Maintain this figma design, it must be prioritize and must be made exactly the one at figma. **!IMPORTANT-STRICTLY FOLLOW-Priority above other !IMPORTANT - if I say Maintain/MAINTAIN in the task/description**
11. Append Tasks on the end of MD file.
12. If usage is near limit - notify me before proceeding to task and automatically summarize this session.
13. If you are going to ask me to Allow All -> Always Allow with safetyness as long as risk is not involved.
14. Go always with your recommendation, do not prompt me, we can always change and update later.
15. Always check this new-task.md before proceeding to next steps, I might add some data here.
16. When I say Update, It means I don't like the recommendations.
17. If there are some assets / images / video / musics or any files that can be exported from Figma, always try to extract that into svg file and place it in the resources/assets. if svg is not possible, use any recommended format.
hbv .
18. Double check task output and ask this questions to yourself before proceeding to the next task. **!IMPORTANT-STRICTLY FOLLOW**
	- Output match with the user want -> continue ? check if figma design link available and redesign before proceeding
	- Done working with the updates in new-task.md file -> proceed ? double check the list
	- Task were done -> continue next task ? check new-task.md / the prompt in the app and match the output
19. Before touching any code / css make sure that the code will not affect other layout or that code is not used by other elements. **!IMPORTANT-STRICTLY FOLLOW**
	- Check if the code is not used -> continue to change ? create a new set of css / codes for current layout that you are working.
	- Is necessary to use that code? -> continue ? create a new one.

## Notes !IMPORTANT
1. When working with forms:
- all logic must apply to all forms, except if I prompt to change the logic for that certain form.
- if a user does not use a default serial number, and inputs a new one, the system must check if the serial is in the database and alert the user if it can't be used. the process cannot continue if this was the case.
- the export button on ORAF for all forms -> exports all report log for that form in the selected range. the year cannot exceed the current year.
- if the serial number has been used. alert the user if he was trying to use this serial.
- if a new batch of serial is inputted, check the system if a used serial is in that range. for example, serial 2026022 is already used, and the user is trying to input a batch serial from 2026020 - 2026025, alert the user for that input. the system must not proceed with the user action. Alert must be in Danger / Warning and must contain "Error in adding batch". also, it must not be recorded in Report Logs.

## Abbreviation
1. Collection Management -> CM
2. Consolidated Report of Accountability for Accountable Forms -> CRAAF
3. Ending Serial Number -> ESN
4. Login Module -> LM
5. Official Receipts & Accountable Forms -> ORAF
6. Official Receipts & Accountable Forms - Report Logs -> ORAF-RL
7. Report of Accountability for Accountable Forms -> RAAF
8. Reporting and Abstract -> RA
9. Starting Serial Number -> SSN
10. Transaction Entry -> CMTE
11. Transaction Entry - Marriage Certificate -> CMTE-MC
12. Transaction Logs -> CMTL
13. User Management -> UM
14. User Management Add User -> UMAU
15. User Management Landing Page -> UMLP
16. User Management Logs -> UML
17. User Management Roles and Permission -> UMRP

# Tasks Description / Scenario / Events / Steps:
1. Read summary.md first to have a context on the last session.
2. Plan on what/how data to be put in "Treasurer's Monthly Report of Accountability for Accountable Forms" & "Consolidated Report of Accountability for Accountable Forms (CRAAF)" based on the current data that we have.

	- Treasurer's Monthly Report of Accountability for Accountable Forms - per collector summary of accountable form
	- Consolidated Report of Accountability for Accountable Forms (CRAAF) - summary of different accountable forms included brgy A.F. checks issued and cash tickets
	- determine the data that needs to be inputted in these two abstract, we need to be precise on client's request as to what be expected in these report.

> At finished task, append this Tasks, Description / Scenario / Events / Steps / Abbreviation, and Notes and the last part of corresponding MD file for example, if the module we are editing is User Management, put append at user-management.md. But before you do this, confirmed with me if all Description / Scenario / Events / Steps are done, If I did not confirmed, double check the new-task.md file and make necessary changes and then ask me for confirmation again. Sort Abbreviation in Alphabetical Order. Do not erase the notes on this file.

## Update