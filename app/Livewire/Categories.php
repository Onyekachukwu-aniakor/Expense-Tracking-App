<?php

namespace App\Livewire;

use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
#[Title(content:"Categories ")]
class Categories extends Component
{

//below are default values
    public  $name = '';
    public $color = '#3B82F6';
    public $icon ='';
    public $editingId= null;
    public $isEditing= false;

    public $colors = [
        '#EF4444', // Red
        '#F97316', // Orange
        '#F59E0B', // Amber
        '#EAB308', // Yellow
        '#84CC16', // Lime
        '#22C55E', // Green
        '#10B981', // Emerald
        '#14B8A6', // Teal
        '#06B6D4', // Cyan
        '#0EA5E9', // Sky
        '#3B82F6', // Blue
        '#6366F1', // Indigo
        '#8B5CF6', // Violet
        '#A855F7', // Purple
        '#D946EF', // Fuchsia
        '#EC4899', // Pink
        '#F43F5E', // Rose
    ];

    protected function rules(): array{
        return [
        'name' => 'required|string|max:255|unique:categories,name,' . ($this->editingId ?: 'NULL') . ',id,user_id,' . Auth::user()->id,
        'color' => 'required|string',
        'icon'=>'nullable|max:255|string',
        ];
    }
//used to customise messages
    protected $messages = [
        'name.required'=>'Please enter a category name',
        'color.unique'=>'You already have a category with this name',
        'icon.required'=>'Please select a color'
    ];
 #[Computed]
    public function categories():mixed{
        return Category::withCount(relations:'expenses')
        ->where(column:'user_id', operator: Auth::user()->id)
        ->orderBy('name')
        ->get();
    }

    public function edit($categoryId): void{
        $category = Category::findOrFail(id: $categoryId);
        if($category->user_id !== Auth::user()->id){
            abort(code:403);
        }

        $this->editingId = $category->id;
        $this->name = $category->name;
        $this->color=$category->color;
        $this->icon= $category->icon;
        $this->isEditing= true;
    }

     public function save(){
        $this->validate();

        if($this->isEditing && $this->editingId){
            $category = Category::findOrFail($this->editingId);

            if ($category->user_id !== Auth::user()->id) {
               abort(403);
            }

            $category->update([
                'name' => $this->name,
                'color' => $this->color,
                'icon' => $this->icon,
            ]);
            session()->flash('message','Category updated successfully.');
        }else{
        //creating
            Category::create([
                'user_id' => Auth::user()->id,
                'name' => $this->name,
                'color' => $this->color,
                'icon' => $this->icon,
            ]);
            session()->flash(key:'message',value:'Category created successfully.');
        }
        $this->reset(['name','color','icon','editingId','isEditing']);
    }

    public function cancelEdit(): void{
         $this->reset(['name', 'color', 'icon','editingId', 'isEditing']);
         $this->color = "#3B82F6";
    }

    public function delete($categoryId): void{
        $category = Category::findOrFail($categoryId);
        if($category->user_id !==Auth::user()->id){
            abort(code:403);
        }

        //check if category has expense
        if($category->expenses()->count() > 0){
            session()->flash(key: 'error', value:'Cannot delete category with existing expenses');
            return;
        }
        $category->delete();
        session()->flash(key:'message', value: 'Category deleted successfully');
    }
    
    public function render()
    {
        return view('livewire.categories', data:[
            'categories' =>$this->categories
        ]);
    }
}
