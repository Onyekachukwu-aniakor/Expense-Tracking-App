<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Expense;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
#[Title(content:"Recurring Expenses - ExpenseTracker")]
class RecurringExpense extends Component
{
    public $showDeleteModal = false;
    public $expenseToDelete = null;

    public function confirmDelete($expenseId){
        $this->expenseToDelete = $expenseId;
        $this->showDeleteModal = true;
    }

    public function deleteExpense(){
        if($this->expenseToDelete){
            $expense = Expense::findOrFail(id:$this->expenseToDelete);
            if($expense->user_id !== Auth::user()->id){
                abort(403);
            }
            // delete the dependent expenses
            $expense->childExpenses()->delete();
            $expense->delete();

            session()->flash('message','Recurring Expense deleted successfully!');

            $this->showDeleteModal = false;
            $this->expenseToDelete = null;
        }
    }
    #[Computed()]
    public function recurringExpenses(){
        return Expense::with(relations:['category','childExpenses'])
        ->forUser(userId:Auth::user()->id)
        ->recurring()
        ->get();
    }
    #[Computed()]
    public function categories(){
        return Category::where(column:'user_id',operator: Auth::user()->id)
        ->orderBy(column:'name')
        ->get();
    }

    public function render()
    {
        return view('livewire.recurring-expense', [
            'recurringExpenses' => $this->recurringExpenses(),
            'categories' => $this->categories()
        ]);
    }
}
