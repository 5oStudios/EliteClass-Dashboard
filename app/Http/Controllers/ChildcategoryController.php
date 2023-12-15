<?php

namespace App\Http\Controllers;

use DB;
use App\BBL;
use App\Course;
use App\Categories;
use App\SubCategory;
use App\ChildCategory;
use App\OfflineSession;
use App\secondaryCategory;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class ChildcategoryController extends Controller
{
   
    public function __construct()
    {
         return $this->middleware('auth');
    }
    

    public function index(Request $request)
    {
        abort_if(!auth()->user()->can('childcategories.view'),403,'User does not have the right permissions.');

        $majorCategories = ChildCategory::latest('id');
        $categories = Categories::where('status', 1)->latest('id')->get();

        if ($request->ajax()) {
            return DataTables::of($majorCategories)
                ->addIndexColumn()
                ->addColumn('checkbox', function ($row) {

                    $chk = "<div class='inline'>
                              <input type='checkbox' form='bulk_delete_form' class='filled-in material-checkbox-input' name='checked[]'' value='$row->id' id='checkbox$row->id'>
                              <label for='checkbox$row->id' class='material-checkbox'></label>
                            </div>";

                    return $chk;
                })

                ->editColumn('image', 'admin.category.childcategory.datatables.image')
                ->editColumn('title', function ($row) {

                    return $row->title ?? '';
                })
                ->editColumn('slug', function ($row) {

                    return $row->slug ?? '';
                })
                ->editColumn('status', function ($row) {
                    
                    return "<span class='btn btn-rounded " . ($row->status == 1 ? 'btn-success-rgba' : 'btn-danger-rgba') . "'>
                            " . ($row->status ? __('adminstaticword.Active') : __('adminstaticword.Deactive')) . "
                        </span>";
                })
                ->editColumn('action', 'admin.category.childcategory.datatables.action')
                ->rawColumns(['checkbox', 'image', 'title', 'slug', 'status', 'action'])
                ->make(true);
        }

        return view('admin.category.childcategory.index', compact('categories'));
    }


    public function create()
    {
        abort_if(!auth()->user()->can('childcategories.create'),403,'User does not have the right permissions.');

        $category = Categories::where('status',true)->get();
        $subcategory = SubCategory::where('status',true)->get();
        $childcategory = ChildCategory::where('status',true)->get();
        return view('admin.category.childcategory.insert',compact('category', 'subcategory', 'childcategory')); 
    }


    public function store(Request $request)
    {
        abort_if(!auth()->user()->can('childcategories.create'),403,'User does not have the right permissions.');

        $request->validate([
            "category_id"=>"required",
            "scnd_category_id"=>"required",
            "subcategories"=>"required",
            "title"=>"required|unique:child_categories,title",
            'image' => 'required|mimes:jpg,jpeg,png|max:10240',
        ],[
            "category_id.required"=>__('Country name is required'),
            "scnd_category_id.required"=> __('Type of institute name is required'),
            "subcategories.required"=> __('Institute name is required'),
            "title.required"=>__('Major name is required'),
            "title.unique" => __('This major name is already exist'),
            'image.required' => __('Image is required'),
            'image.mimes' => __('Image must be a type of jpeg, jpg or png'),
            'image.max' => __('Image size should not be more than 10 MB'),
        ]);


        $input = $request->all();

        if ($file = $request->file('image')) {

            $path = 'images/majorcategory/';

            if(!file_exists(public_path().'/'.$path)) {
              
              $path = 'images/majorcategory/';
              File::makeDirectory(public_path().'/'.$path,0777,true);
            }   

            $optimizeImage = Image::make($file);
            $optimizePath = public_path() . '/images/majorcategory/';
            $image = time() . $file->getClientOriginalName();
            $optimizeImage->save($optimizePath . $image, 72);
  
            $input['image'] = $image;
        }

        $input['category_id'] = $request->category_id;
        $input['subcategory_id'] = $request->subcategories;
        $input['title'] = $request->title;
        $input['icon'] = $request->icon;
        $input['status'] = isset($request->status) ? 1 : 0;
       
        if (app()->getLocale() == 'en') {
            $slug = str_slug($request->title, '-');
            $input['slug'] = $slug;
        }

        ChildCategory::create($input);

        // Session::flash('success',trans('flash.AddedSuccessfully'));
        return redirect('childcategory')->with('success', trans('flash.AddedSuccessfully'));
    }


    public function show($id)
    {
        abort('404');
    }


    public function edit($id)
    {
        abort_if(!auth()->user()->can('childcategories.edit'),403,'User does not have the right permissions.');

        $cate = ChildCategory::findOrFail($id);
        $category = Categories::where('status', 1)->get();
        $typecategory = secondaryCategory::where('status', 1)->where('category_id',$cate->category_id)->get();
        $subcategory = SubCategory::where('status', 1)->where(['category_id'=>$cate->category_id,'scnd_category_id'=>$cate->scnd_category_id])->get();
        
        return view('admin.category.childcategory.edit',compact('cate', 'category', 'typecategory', 'subcategory'));
    }


    public function update(Request $request,$id)
    {
        abort_if(!auth()->user()->can('childcategories.edit'),403,'User does not have the right permissions.');

        $request->validate([
            "category_id"=>"required",
            "scnd_category_id"=>"required",
            "subcategory_id"=>"required",
            "title"=>"required|unique:child_categories,title,".$id,
            'image' => 'mimes:jpg,jpeg,png|max:10240',
        ],[
            "category_id.required"=>__('Country name is required'),
            "scnd_category_id.required"=> __('Type of institute name is required'),
            "subcategories.required"=> __('Institute name is required'),
            "title.required"=>__('Major name is required'),
            "title.unique" => __('This major name is already exist'),
            'image.mimes' => __('Image must be a type of jpeg, jpg or png'),
            'image.max' => __('Image size should not be more than 10 MB'),
        ]);
        
        $data = ChildCategory::findorfail($id);
        $input = $request->all();

        if ($file = $request->file('image')) {

            $path = 'images/majorcategory/';

            if(!file_exists(public_path().'/'.$path)) {
              
              $path = 'images/majorcategory/';
              File::makeDirectory(public_path().'/'.$path,0777,true);
            }   

            if ($data->image != null) {
                $content = @file_get_contents(public_path() . '/images/majorcategory/' . $data->image);
                if ($content) {
                    unlink(public_path() . '/images/majorcategory/' . $data->image);
                }
            }
  
            $optimizeImage = Image::make($file);
            $optimizePath = public_path() . '/images/majorcategory/';
            $image = time() . $file->getClientOriginalName();
            $optimizeImage->save($optimizePath . $image, 72);
  
            $input['image'] = $image;
        }

        $input['status'] = isset($request->status) ? 1 : 0;


        if (app()->getLocale() == 'en') {
            $slug = str_slug($request->title, '-');
            $input['slug'] = $slug;
        }
        
        $data->update($input);
        
        // Session::flash('success',trans('flash.UpdatedSuccessfully'));
        return redirect('childcategory')->with('success', trans('flash.UpdatedSuccessfully'));
    }


    public function destroy($id)
    {
        abort_if(!auth()->user()->can('childcategories.delete'),403,'User does not have the right permissions.');

        $category = ChildCategory::findorfail($id);
        $course = Course::whereJsonContains('childcategory_id', strval($id))->exists();
        $meeting = BBL::whereJsonContains('ch_sub_category', strval($id))->exists();
        $session = OfflineSession::whereJsonContains('ch_sub_category', strval($id))->exists();

        if($course) {
            return back()->with('delete',trans('flash.CannotDeleteCategory'));
        }
        else if($meeting || $session) {
            return back()->with('delete',trans('flash.CannotDeleteCate'));
        } else {
            if(File::exists('images/majorcategory/'.$category->image)){
                File::delete('images/majorcategory/'.$category->image);
                $category->delete();
            }else{
                $category->delete();
            }
            return back()->with('delete',trans('flash.DeletedSuccessfully'));
        }
    
        return redirect('childcategory');
    }


    public function status(Request $request)
    {
        abort_if(!auth()->user()->can('childcategories.edit'),403,'User does not have the right permissions.');

        $data = ChildCategory::find($request->id);
        $data->status = $request->status;
        $data->save();
        return back()->with('success',trans('flash.UpdatedSuccessfully'));
    }


    public function bulk_delete(Request $request)
    {
        abort_if(!auth()->user()->can('childcategories.delete'),403,'User does not have the right permissions.');

           $validator = Validator::make($request->all(), ['checked' => 'required']);
           if ($validator->fails()) {
            return back()->with('error',trans('Please select field to be deleted.'));
           
           }else{

            foreach($request->checked as $id){
               $this->destroy($id);
            }
            //   ChildCategory::whereIn('id',$request->checked)->delete();
          }

        //   return back()->with('error',trans('Selected ChildCategory has been deleted.'));
        return back();  
   }
}
