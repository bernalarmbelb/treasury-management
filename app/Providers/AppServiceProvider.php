<?php

namespace App\Providers;

use App\Models\FormStock;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Transaction-entry forms a quick-entry link can be created for, keyed by
     * the form's stock code. The order here is the order links appear in the
     * footer bar.
     */
    private const QUICK_ENTRY_FORMS = [
        'Form 5IC' => ['label' => 'Official Receipt', 'route' => 'transaction-entry.official-receipt'],
        'BIR0016' => ['label' => 'Individual Cedula', 'route' => 'transaction-entry.individual-cedula'],
        'BIR0017' => ['label' => 'Corporation Cedula', 'route' => 'transaction-entry.corporation-cedula'],
        'Form 56' => ['label' => 'OR / RPT', 'route' => 'transaction-entry.or-rpt'],
        'Form 10' => ['label' => 'Marriage Certificate', 'route' => 'transaction-entry.marriage-certificate'],
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('components.layout', function ($view) {
            $view->with('quickEntryForms', $this->quickEntryForms());
        });
    }

    /**
     * The transaction-entry quick links shown in the footer bar: one per form
     * type that currently has available stock, with its remaining quantity and
     * the route to start a new transaction for it. Empty for guests.
     */
    private function quickEntryForms(): \Illuminate\Support\Collection
    {
        if (! auth()->check()) {
            return collect();
        }

        $forms = FormStock::query()
            ->whereIn('form_code', array_keys(self::QUICK_ENTRY_FORMS))
            ->with([
                'batches',
                'ctcIndividualTransactions',
                'ctcCorporationTransactions',
                'orRptTransactions',
                'orTransactions',
                'marriageCertificateTransactions',
            ])
            ->get();

        $links = collect();

        foreach (self::QUICK_ENTRY_FORMS as $code => $meta) {
            $available = $forms
                ->where('form_code', $code)
                ->filter(fn (FormStock $form) => $form->availableQty() > 0);

            if ($available->isEmpty()) {
                continue;
            }

            $links->push([
                'label' => $meta['label'],
                'qty' => $available->sum(fn (FormStock $form) => $form->availableQty()),
                // Relative path (absolute: false) so links resolve against the
                // current host — works on the real domain and the localhost preview.
                'url' => route($meta['route'], $available->first()->id, false),
            ]);
        }

        return $links;
    }
}
