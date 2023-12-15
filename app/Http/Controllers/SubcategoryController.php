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

class SubcategoryController extends Controller {

    public function __construct() {
        return $this->middleware('auth');
    }


    public function index(Request $request) {
        abort_if(!auth()->user()->can('subcategories.view'), 403, 'User does not have the right permissions.');

        $instituteCategories = SubCategory::latest('id');
        $categories = Categories::where('status', 1)->latest('id')->get();

        if ($request->ajax()) {
            return DataTables::of($instituteCategories)
                ->addIndexColumn()
                ->addColumn('checkbox', function ($row) {

                    $chk = "<div class='inline'>
                              <input type='checkbox' form='bulk_delete_form' class='filled-in material-checkbox-input' name='checked[]'' value='$row->id' id='checkbox$row->id'>
                              <label for='checkbox$row->id' class='material-checkbox'></label>
                            </div>";

                    return $chk;
                })

                ->editColumn('image', 'admin.category.subcategory.datatables.image')
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
                ->editColumn('action', 'admin.category.subcategory.datatables.action')
                ->rawColumns(['checkbox', 'image', 'title', 'slug', 'status', 'action'])
                ->make(true);
        }

        return view('admin.category.subcategory.index', compact('categories'));
    }


    public function create() {
        abort_if(!auth()->user()->can('subcategories.create'), 403, 'User does not have the right permissions.');
        $category = Categories::where('status', 1)->get();
        $scnd_category = secondaryCategory::where('status', 1)->get();
        $subcategory = SubCategory::where('status', 1)->get();
        return view('admin.category.subcategory.insert', compact('subcategory'));
    }


    public function store(Request $request) {

        abort_if(!auth()->user()->can('subcategories.create'), 403, 'User does not have the right permissions.');
        
        $request->validate([
            "category_id" => "required",
            "scnd_category_id" => "required",
            "title" => "required|unique:categories,title",
            'image' => 'required|mimes:jpg,jpeg,png|max:10240',
        ],[
            "category_id.required" => __('Country name is required'),
            "scnd_category_id.required" => __('Type of institute name is required'),
            "title.required" => __('Institute name is required'),
            "title.unique" => __('This institute name is already exist'),
            'image.required' => __('Image is required'),
            'image.mimes' => __('Image must be a type of jpeg, jpg or png'),
            'image.max' => __('Image size should not be more than 10 MB'),
        ]);

        $input = $request->all();

        if ($file = $request->file('image')) {

            $path = 'images/institutecategory/';

            if(!file_exists(public_path().'/'.$path)) {
              
              $path = 'images/institutecategory/';
              File::makeDirectory(public_path().'/'.$path,0777,true);
            }   

            $optimizeImage = Image::make($file);
            $optimizePath = public_path() . '/images/institutecategory/';
            $image = time() . $file->getClientOriginalName();
            $optimizeImage->save($optimizePath . $image, 72);
  
            $input['image'] = $image;
        }

        if (app()->getLocale() == 'en') {
            $slug = str_slug($request->title, '-');
            $input['slug'] = $slug;
        }
         
        // $slug = str_slug($input['title'],'-');
        $input['status'] = isset($request->status) ? 1 : 0;
        $data = SubCategory::create($input);

        $data->save();

        // Session::flash('success', trans('flash.AddedSuccessfully'));
        return redirect('subcategory')->with('success', trans('flash.AddedSuccessfully'));
    }


    public function show($id) {
        abort(404);
    }


    public function edit($id) {
        abort_if(!auth()->user()->can('subcategories.edit'), 403, 'User does not have the right permissions.');

        $cate = SubCategory::findOrFail($id);
        $category = Categories::where('status', 1)->get();
        $typecategory = secondaryCategory::where('status', 1)->where('category_id',$cate->category_id)->get();

        return view('admin.category.subcategory.edit', compact('cate', 'category', 'typecategory'));
    }


    public function update(Request $request, $id) {
        abort_if(!auth()->user()->can('subcategories.edit'), 403, 'User does not have the right permissions.');

        $request->validate([
            "category_id" => "required",
            "scnd_category_id" => "required",
            "title" => "required|unique:categories,title,".$id,
            'image' => 'mimes:jpg,jpeg,png|max:10240',
        ],[
            "category_id.required" => __('Country name is required'),
            "scnd_category_id.required" => __('Type of institute name is required'),
            "title.required" => __('Institute name is required'),
            "title.unique" => __('This institute name is already exist'),
            'image.mimes' => __('Image must be a type of jpeg, jpg or png'),
            'image.max' => __('Image size should not be more than 10 MB'),
        ]);


        $data = SubCategory::findorfail($id);
        $input = $request->all();

        if ($file = $request->file('image')) {

            $path = 'images/institutecategory/';

            if(!file_exists(public_path().'/'.$path)) {
              
              $path = 'images/institutecategory/';
              File::makeDirectory(public_path().'/'.$path,0777,true);
            }   

            if ($data->image != null) {
                $content = @file_get_contents(public_path() . '/images/institutecategory/' . $data->image);
                if ($content) {
                    unlink(public_path() . '/images/institutecategory/' . $data->image);
                }
            }
  
            $optimizeImage = Image::make($file);
            $optimizePath = public_path() . '/images/institutecategory/';
            $image = time() . $file->getClientOriginalName();
            $optimizeImage->save($optimizePath . $image, 72);
  
            $input['image'] = $image;
        }

        // $slug = str_slug($input['title'],'-');
        // $input['slug'] = $slug;

        $input['status'] = isset($request->status) ? 1 : 0;

        if (app()->getLocale() == 'en') {
            $slug = str_slug($request->title, '-');
            $input['slug'] = $slug;
        }

        $data->update($input);
        
        // Session::flash('success', trans('flash.UpdatedSuccessfully'));
        return redirect('subcategory')->with('success', trans('flash.UpdatedSuccessfully'));
    }


    public function destroy($id) {
        abort_if(!auth()->user()->can('subcategories.delete'), 403, 'User does not have the right permissions.');
        
        $category = SubCategory::findorfail($id);
        $course = Course::where('subcategory_id', $id)->exists();
        $meeting = BBL::where('sub_category', $id)->exists();
        $session = OfflineSession::where('sub_category', $id)->exists();

        if ($course) {
            return back()->with('delete', trans('flash.CannotDeleteCategory'));
        } else if ($meeting || $session) {
            return back()->with('delete', trans('flash.CannotDeleteCate'));
        } else {
            if (File::exists('images/institutecategory/'.$category->image)) {
                File::delete('images/institutecategory/'.$category->image);
                $category->delete();
                ChildCategory::where('subcategory_id', $id)->delete();
            } else {
                $category->delete();
                ChildCategory::where('subcategory_id', $id)->delete();
            }

            return back()->with('delete', trans('flash.DeletedSuccessfully'));
        }

        return redirect('subcategory');
    }


    public function SubcategoryStore(Request $request) {
        abort_if(!auth()->user()->can('subcategories.create'), 403, 'User does not have the right permissions.');
        
        $cat = new SubCategory;

        $cat->category_id = $request->categories;

        $cat->title = $request->title;

        $cat->icon = $request->icon;

        $cat->status = $request->status;

        $slug = str_slug($request['title'], '-');
        $cat['slug'] = $slug;

        $cat->save();

        return back()->with('success', trans('flash.AddedSuccessfully'));
    }


    public function status(Request $request) {
        abort_if(!auth()->user()->can('subcategories.edit'), 403, 'User does not have the right permissions.');

        $cat = SubCategory::find($request->id);
        $cat->status = $request->status;
        $cat->save();
        return back()->with('success', trans('flash.UpdatedSuccessfully'));
    }
    

    public function bulk_delete(Request $request) {
        abort_if(!auth()->user()->can('subcategories.delete'), 403, 'User does not have the right permissions.');
        $validator = Validator::make($request->all(), ['checked' => 'required']);

        if ($validator->fails()) {
            return back()->with('error', trans('Please select field to be deleted.'));

        }else{

            foreach($request->checked as $id){
                $this->destroy($id);
            }
            // SubCategory::whereIn('id', $request->checked)->delete();
            // ChildCategory::whereIn('subcategory_id', $request->checked)->delete();
        }

        // return back()->with('error', trans('Selected SubCategory has been deleted.'));
        return back();
    }

}
