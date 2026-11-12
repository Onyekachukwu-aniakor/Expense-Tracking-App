<?php

namespace App\Livewire;

use App\Models\Budget;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
#[Title(content:"Budgets - ExpenseTracker")]
class BudgetList extends Component
{

public $selectedMonth;
public $selectedYear;

public $showCreateModel = false;

public function mount():void{
    //initialize the above
    $this->selectedMonth = now()->month;
    $this->selectedYear = now()->year;
}

#[Computed]
public function budgets(){
        return Budget::with('category')
        ->where('user_id', Auth::user()->id)
        ->where('month', $this->selectedMonth)
        ->where('year', $this->selectedYear)
        ->get()
        //mapping what displays on the ui
        ->map(function ($budget) {
                $budget->spent = $budget->getSpentAmount();
                $budget->remaining = $budget->getRemainingAmount();
                $budget->percentage = $budget->getPercentageUsed();
                $budget->is_over = $budget->isOverBudget();
                return $budget;
            });
}
 #[Computed]
    public function totalBudget(){
        return $this->budgets->sum('amount');
    }

    #[Computed]
    public function totalSpent(){
        return $this->budgets->sum('spent');
    }

    #[Computed]
    public function totalRemaining(){
        return $this->budgets->sum('remaining');
    }

    #[Computed]
    public function overallPercentage(){
        if ($this->totalBudget == 0) {
            return 0;
        }

        return round(($this->totalSpent / $this->totalBudget) * 100,1);
    }

    #[Computed]
    public function categories(){
        return Category::where('user_id', Auth::user()->id)
                ->orderBy('name')
                ->get();
    }
    //below is used to navigate to pre & next month
    public function previousMonth(){
        $date = Carbon::create(year: $this->selectedYear,month: $this->selectedMonth, day: 1)->subMonth();
        $this->selectedMonth = $date->month;
        $this->selectedYear = $date->year;
    }

    public function nextMonth()
    {
        $date = Carbon::create(year:$this->selectedYear,month: $this->selectedMonth,day: 1)->addMonth();
        $this->selectedMonth = $date->month;
        $this->selectedYear = $date->year;
    }

    public function setCurrentMonth(){
        $this->selectedMonth = now()->month;
        $this->selectedYear = now()->year;
    }

    public function deleteBudget($budgetId){
        $budget = Budget::findOrFail($budgetId);
        if ($budget->user_id !== Auth::user()->id) {
           abort(403);
        }
         //else 
        $budget->delete();
        session()->flash('message','Budget deleted succssfully.');
    }
    public function render()
    {
        return view('livewire.budget-list', data: [
            'budgets'=>$this->budgets,
            'totalBudget'=>$this->totalBudget,
            'totalSpent'=>$this->totalSpent,
            'totalRemaining'=>$this->totalRemaining,
            'overallPercentage'=>$this->overallPercentage,
            'categories'=>$this->categories,

        ]);
    }
}
