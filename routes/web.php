<?php

use App\Livewire\BudgetForm;
use App\Livewire\BudgetList;
use App\Livewire\Categories;
use App\Livewire\Dashboard;
use App\Livewire\ExpenseForm;
use App\Livewire\ExpenseList;
use App\Livewire\RecurringExpense;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    //Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::get('dashboard', Dashboard::class)->name('dashboard');
    Route::get(uri:'categories', action: Categories::class)->name(name:'categories.index');
    //budgets
    Route::get(uri:'budgets', action: BudgetList::class)->name(name:'budgets.index');
    Route::get(uri:'budgets/create', action: BudgetForm::class)->name(name:'budgets.create');
    Route::get(uri:'budgets/{budgetId}/edit', action: BudgetForm::class)->name(name:'budgets.edit');

    //expenses
    Route::get(uri:'expenses', action: ExpenseList::class)->name(name:'expenses.index');
    Route::get(uri:'/expenses/create', action: ExpenseForm::class)->name(name:'expenses.index');
    Route::get(uri:'expenses/{expenseId}/edit', action: ExpenseForm::class)->name(name:'expenses.edit');
    Route::get('recurring-expenses',RecurringExpense::class)->name('recurring-expenses.index');
});

require __DIR__.'/settings.php';
