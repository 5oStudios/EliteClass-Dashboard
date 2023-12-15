<?php

namespace App\Http\Controllers;

use DB;
use App\BBL;
use App\Course;
use App\Categories;
use App\SubCategory;
use App\ChildCategory;
use App\CourseLanguage;
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

class CategoriesController extends Controller
{   
    public function __construct()
    {
      $this->middleware('permission:categories.view', ['only' => ['index','show']]);
        $this->middleware('permission:categories.create', ['only' => ['create', 'store']]);
        $this->middleware('permission:categories.edit', ['only' => ['edit', 'update','catstatus']]);
        $this->middleware('permission:categories.delete', ['only' => ['destroy', 'bulk_delete']]);
    
    }


    public function index(Request $request)
    {
      // $flags = [];
      // if ($handle = opendir(public_path('flags/128x128'))) {
      //     while (false !== ($entry = readdir($handle))) {
      //         if ($entry != '.' && $entry != '..') {
      //             $flags[] = ["id"=>$entry,"text"=>$entry];
      //         }
      //     }
      //     closedir($handle);
      // }
      // $flags = ["results"=>$flags];

      $categories = Categories::latest('id');

      if ($request->ajax()) {
          return DataTables::of($categories)
              ->addIndexColumn()
              ->addColumn('checkbox', function ($row) {

                  $chk = "<div class='inline'>
                            <input type='checkbox' form='bulk_delete_form' class='filled-in material-checkbox-input' name='checked[]'' value='$row->id' id='checkbox$row->id'>
                            <label for='checkbox$row->id' class='material-checkbox'></label>
                          </div>";

                  return $chk;
              })

              ->editColumn('image', 'admin.category.datatables.image')
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
              ->editColumn('action', 'admin.category.datatables.action')
              ->rawColumns(['checkbox', 'image', 'title', 'slug', 'status', 'action'])
              ->make(true);
      }

      return view('admin.category.index');
    }
  

    public function store(Request $request)
    {
      $request->validate([
          "title"=>"required|unique:categories,title",
          'cat_image' => 'required|mimes:jpg,jpeg,png|max:10240',
      ],[
          "title.required"=> __('Country name is required'),
          "title.unique" => __('This country name is already exist'),
          'cat_image.required' => __('Image is required'),
          'cat_image.mimes' => __('Image must be a type of jpeg, jpg or png'),
          'cat_image.max' => __('Image size should not be more than 10 MB'),
      ]);

      $input = $request->all();
      // $slug = str_slug($input['title'],'-');
      // $input['slug'] = $slug;
      // $input['position'] = (Categories::count()+1);

      if ($file = $request->file('cat_image')) {

        $path = 'images/category/';

        if(!file_exists(public_path().'/'.$path)) {
          
          $path = 'images/category/';
          File::makeDirectory(public_path().'/'.$path,0777,true);
        }   

        $optimizeImage = Image::make($file);
        $optimizePath = public_path() . '/images/category/';
        $image = time() . $file->getClientOriginalName();
        $optimizeImage->save($optimizePath . $image, 72);

        $input['cat_image'] = $image;
      }

      if (app()->getLocale() == 'en') {
        $slug = str_slug($request->title, '-');
        $input['slug'] = $slug;
      }
      
      $input['status'] = isset($request->status)  ? 1 : 0;
      $input['featured'] = isset($request->featured)  ? 1 : 0;

      Categories::create($input);

      // Session::flash('success', trans('flash.AddedSuccessfully'));
      return redirect('category')->with('success', trans('flash.AddedSuccessfully'));
    }
    

    public function show($id)
    {       
        abort('404');
    }


    public function edit($id)
    {
      $cate = Categories::findOrFail($id);
      
      // $flags = [];
      // if ($handle = opendir(public_path('flags/128x128'))) {
      //     while (false !== ($entry = readdir($handle))) {
      //         if ($entry != '.' && $entry != '..') {
      //             $flags[] = ["id"=>$entry,"text"=>$entry];
      //         }
      //     }
      //     closedir($handle);
      // }

      return view('admin.category.edit', compact('cate'));
    }


    public function update(Request $request,$id)
    {
      $request->validate([
          "title"=>"required|unique:categories,title,".$id,
          'cat_image' => 'mimes:jpg,jpeg,png|max:10240',
      ],[
          "title.required"=> __('Country name is required'),
          "title.unique" => __('This country name is already exist'),
          'cat_image.mimes' => __('Image must be a type of jpeg, jpg or png'),
          'cat_image.max' => __('Image size should not be more than 10 MB'),
      ]);

      $data = Categories::findOrFail($id);
      $input = $request->all();

      if ($file = $request->file('cat_image')) {

        $path = 'images/category/';

        if(!file_exists(public_path().'/'.$path)) {
          
          $path = 'images/category/';
          File::makeDirectory(public_path().'/'.$path,0777,true);
        }   

        if ($data->cat_image != null) {
            $content = @file_get_contents(public_path() . '/images/category/' . $data->cat_image);
            if ($content) {
                unlink(public_path() . '/images/category/' . $data->cat_image);
            }
        }

        $optimizeImage = Image::make($file);
        $optimizePath = public_path() . '/images/category/';
        $image = time() . $file->getClientOriginalName();
        $optimizeImage->save($optimizePath . $image, 72);

        $input['cat_image'] = $image;
      }

      $input['status'] = isset($request->status) ? 1 : 0;
      $input['featured'] = isset($request->featured) ? 1 : 0;

      if (app()->getLocale() == 'en') {
        $slug = str_slug($request->title, '-');
        $input['slug'] = $slug;
      }

      $data->update($input);

      // Session::flash('success',trans('flash.UpdatedSuccessfully'));
      return redirect('category')->with('success', trans('flash.UpdatedSuccessfully'));
    }

    
    public function destroy($id)
    {
      $category = Categories::findOrFail($id);
      $course = Course::where('category_id', $id)->exists();
      $meeting = BBL::where('main_category', $id)->exists();
      $session = OfflineSession::where('main_category', $id)->exists();

      if($course) {
        return back()->with('delete',trans('flash.CannotDeleteCategory'));
      } else 
      if($meeting || $session) {
        return back()->with('delete',trans('flash.CannotDeleteCate'));
      } else {
          if (File::exists('images/category/'.$category->cat_image)) {
            File::delete('images/category/'.$category->cat_image);
            $category->delete();
            secondaryCategory::where('category_id', $id)->delete();
            SubCategory::where('category_id', $id)->delete();
            ChildCategory::where('category_id', $id)->delete();
          } else {
            $category->delete();
            secondaryCategory::where('category_id', $id)->delete();
            SubCategory::where('category_id', $id)->delete();
            ChildCategory::where('category_id', $id)->delete();
          }

          return back()->with('delete',trans('flash.DeletedSuccessfully'));
      }
        
        return redirect('category');
    }
      

    public function bulk_delete(Request $request)
    {
      $validator = Validator::make($request->all(), ['checked' => 'required']);

      if ($validator->fails()) {
        return back()->with('error',trans('Please select field to be deleted.'));

      }else{

        foreach($request->checked as $id){
          $this->destroy($id);
        }
        //  Categories::whereIn('id',$request->checked)->delete();
        //  secondaryCategory::whereIn('category_id', $request->checked)->delete();
        //  SubCategory::whereIn('category_id', $request->checked)->delete();
        //  ChildCategory::whereIn('category_id', $request->checked)->delete();
      }

      // return back()->with('error',trans('Selected Categories has been deleted.'));    
      return back();    
    }


    public function categoryStore(Request $request)
    {
      $cat = new Categories;
      $cat->title = $request->category;
      $cat->icon = $request->icon;

      $cat->slug = $request->slug;

      $cat->position = (Categories::count()+1);
      // $cat->slug = str_slug($request->category);
      $cat->featured = $request->featured;
      $cat->status = $request->status;

      $cat->save();
      return back()->with('success',trans('flash.AddedSuccessfully'));

    }


    public function categoryPage(Request $request)
    {

       $ipaddress = $request->getClientIp();
        
        $geoip = geoip()->getLocation($ipaddress);
        $usercountry = strtoupper($geoip->country);

      
        if(!$request->id && !$request->category){

          return redirect('/')->with('delete', 'Invalid URL');
        }


        $ipaddress = $request->getClientIp();
        
        $geoip = geoip()->getLocation($ipaddress);
        $usercountry = strtoupper($geoip->country);



        $cats = Categories::with('courses')->where('id', $request->id)->first();

        if(!$cats){

          return redirect('/')->with('delete', '404 | category not found !');
        }

        if($request->type){
          // return $request;
          
          $courses = $cats->courses()->where('status', '1')->where('type','=',$request->type == 'paid' ? '1' : '0')->paginate($request->limit ?? 10);

        }else if($request->sortby){

          if($request->sortby == 'l-h'){


            $courses = $cats->courses()->where('status', '1')->where('type','=','1')->orderBy('price','DESC')->paginate($request->limit ?? 10);

          }

          if($request->sortby == 'h-l'){


            $courses = $cats->courses()->where('status', '1')->where('type','=','1')->orderBy('price','ASC')->paginate($request->limit ?? 10);

          }

         

          if($request->sortby == 'a-z'){

            if($request->type)
            {
              $courses = $cats->courses()->where('status', '1')->where('type','=',$request->type == 'paid' ? 1 : 0)->orderBy('title','ASC')->paginate($request->limit ?? 10);
            }
            else{

              $courses = $cats->courses()->where('status', '1')->orderBy('title','ASC')->paginate($request->limit ?? 10);

            }
            

          }

          if($request->sortby == 'z-a'){

            if($request->type)
            {
              $courses = $cats->courses()->where('status', '1')->where('type','=',$request->type == 'paid' ? 1 : 0)->orderBy('title','DESC')->paginate($request->limit ?? 10);
            }
            else{

              $courses = $cats->courses()->where('status', '1')->orderBy('title','DESC')->paginate($request->limit ?? 10);

            }
            


          }

          if($request->sortby == 'newest'){

            if($request->type){

              $courses = $cats->courses()->where('status', '1')->where('type','=',$request->type == 'paid' ? 1 : 0)->orderBy('created_at','DESC')->paginate($request->limit ?? 10);

              
            }else{

              $courses = $cats->courses()->where('status', '1')->orderBy('created_at','DESC')->paginate($request->limit ?? 10);

            }



          }

          if($request->sortby == 'featured'){

            if($request->type)
            {
              $courses = $cats->courses()->where('status', '1')->where('type','=',$request->type == 'paid' ? 1 : 0)->where('featured','=', '1')->paginate($request->limit ?? 10);
            }
            else{

              $courses = $cats->courses()->where('status', '1')->where('featured','=', '1')->paginate($request->limit ?? 10);

            }

            


          }
          
        }else if($request->limit){

          // return 'ghjj';

          if($request->limit == '10'){

            $courses = $cats->courses()->where('status', '1')->paginate(2);

          }
          elseif($request->limit == '30'){

            $courses = $cats->courses()->where('status', '1')->paginate($request->limit);

          }
          elseif($request->limit == '50'){

            $courses = $cats->courses()->where('status', '1')->paginate($request->limit);

          }
          elseif($request->limit == '100'){

            $courses = $cats->courses()->where('status', '1')->paginate($request->limit);

          }
          else{

            $courses = $cats->courses()->where('status', '1')->paginate($request->limit ?? 10);

          }

        }
        else if($request->lang){

          $lang = CourseLanguage::where('id', $request->lang)->first();

          $courses = $cats->courses()->where('status', '1')->where('language_id','=',$lang->id)->paginate($request->limit ?? 10);

        }

        else{

          $courses = $cats->courses()->where('status', '1')->paginate($request->limit ?? 10);

        }

        $filter_count = $courses->count();

        
        $subcat = SubCategory::where('category_id', $cats->id)->get();
       
        return view('front.category',compact('cats', 'courses', 'subcat', 'filter_count', 'usercountry'));
       
    }


    public function subcategoryPage(Request $request)
    {

      $ipaddress = $request->getClientIp();
        
        $geoip = geoip()->getLocation($ipaddress);
        $usercountry = strtoupper($geoip->country);
        
        $cats = SubCategory::where('id', $request->id)->first();

        if(!$cats){

          return redirect('/')->with('delete', '404 | category not found !');
        }

        if($request->type){
          
          $courses = $cats->courses()->where('status', '1')->where('type','=',$request->type == 'paid' ? '1' : '0')->paginate($request->limit ?? 10);

        }else if($request->sortby){

          if($request->sortby == 'l-h'){


            $courses = $cats->courses()->where('status', '1')->where('type','=','1')->orderBy('price','DESC')->paginate($request->limit ?? 10);

          }

          if($request->sortby == 'h-l'){


            $courses = $cats->courses()->where('status', '1')->where('type','=','1')->orderBy('price','ASC')->paginate($request->limit ?? 10);

          }

          if($request->sortby == 'a-z'){

            if($request->type)
            {
              $courses = $cats->courses()->where('status', '1')->where('type','=',$request->type == 'paid' ? 1 : 0)->orderBy('title','ASC')->paginate($request->limit ?? 10);
            }
            else{

              $courses = $cats->courses()->where('status', '1')->orderBy('title','ASC')->paginate($request->limit ?? 10);

            }
            

          }

          if($request->sortby == 'z-a'){

            if($request->type)
            {
              $courses = $cats->courses()->where('status', '1')->where('type','=',$request->type == 'paid' ? 1 : 0)->orderBy('title','DESC')->paginate($request->limit ?? 10);
            }
            else{

              $courses = $cats->courses()->where('status', '1')->orderBy('title','DESC')->paginate($request->limit ?? 10);

            }
            


          }

          if($request->sortby == 'newest'){

            if($request->type){

              $courses = $cats->courses()->where('status', '1')->where('type','=',$request->type == 'paid' ? 1 : 0)->orderBy('created_at','DESC')->paginate($request->limit ?? 10);

              
            }else{

              $courses = $cats->courses()->where('status', '1')->orderBy('created_at','DESC')->paginate($request->limit ?? 10);

            }



          }

          if($request->sortby == 'featured'){

            if($request->type)
            {
              $courses = $cats->courses()->where('status', '1')->where('type','=',$request->type == 'paid' ? 1 : 0)->where('featured','=', '1')->paginate($request->limit ?? 10);
            }
            else{

              $courses = $cats->courses()->where('status', '1')->where('featured','=', '1')->paginate($request->limit ?? 10);

            }

            


          }
          
        }else if($request->limit){

          // return 'ghjj';

          if($request->limit == '10'){

            $courses = $cats->courses()->where('status', '1')->paginate(2);

          }
          elseif($request->limit == '30'){

            $courses = $cats->courses()->where('status', '1')->paginate($request->limit);

          }
          elseif($request->limit == '50'){

            $courses = $cats->courses()->where('status', '1')->paginate($request->limit);

          }
          elseif($request->limit == '100'){

            $courses = $cats->courses()->where('status', '1')->paginate($request->limit);

          }
          else{

            $courses = $cats->courses()->where('status', '1')->paginate($request->limit ?? 10);

          }

        }

        else if($request->lang){

          $lang = CourseLanguage::where('id', $request->lang)->first();

          $courses = $cats->courses()->where('status', '1')->where('language_id','=',$lang->id)->paginate($request->limit ?? 10);

        }

        else{

          $courses = $cats->courses()->where('status', '1')->paginate($request->limit ?? 10);

        }

        $filter_count = $courses->count();

        $childcat = ChildCategory::where('subcategory_id', $cats->id)->get();
        
        return view('front.category',compact('cats', 'courses', 'childcat', 'filter_count', 'usercountry'));

    }


    public function childcategoryPage(Request $request)
    {

      $ipaddress = $request->getClientIp();
        
        $geoip = geoip()->getLocation($ipaddress);
        $usercountry = strtoupper($geoip->country);
        
        $cats = ChildCategory::where('id', $request->id)->first();


        if(!$cats){

          return redirect('/')->with('delete', '404 | category not found !');
        }

        if($request->type){
          
          $courses = $cats->courses()->where('status', '1')->where('type','=',$request->type == 'paid' ? '1' : '0')->paginate($request->limit ?? 10);

        }else if($request->sortby){


          if($request->sortby == 'l-h'){


            $courses = $cats->courses()->where('status', '1')->where('type','=','1')->orderBy('price','DESC')->paginate($request->limit ?? 10);

          }

          if($request->sortby == 'h-l'){


            $courses = $cats->courses()->where('status', '1')->where('type','=','1')->orderBy('price','ASC')->paginate($request->limit ?? 10);

          }

          if($request->sortby == 'a-z'){

            if($request->type)
            {
              $courses = $cats->courses()->where('status', '1')->where('type','=',$request->type == 'paid' ? 1 : 0)->orderBy('title','ASC')->paginate($request->limit ?? 10);
            }
            else{

              $courses = $cats->courses()->where('status', '1')->orderBy('title','ASC')->paginate($request->limit ?? 10);

            }
            

          }

          if($request->sortby == 'z-a'){

            if($request->type)
            {
              $courses = $cats->courses()->where('status', '1')->where('type','=',$request->type == 'paid' ? 1 : 0)->orderBy('title','DESC')->paginate($request->limit ?? 10);
            }
            else{

              $courses = $cats->courses()->where('status', '1')->orderBy('title','DESC')->paginate($request->limit ?? 10);

            }
            


          }

          if($request->sortby == 'newest'){

            if($request->type){

              $courses = $cats->courses()->where('status', '1')->where('type','=',$request->type == 'paid' ? 1 : 0)->orderBy('created_at','DESC')->paginate($request->limit ?? 10);

              
            }else{

              $courses = $cats->courses()->where('status', '1')->orderBy('created_at','DESC')->paginate($request->limit ?? 10);

            }



          }

          if($request->sortby == 'featured'){

            if($request->type)
            {
              $courses = $cats->courses()->where('status', '1')->where('type','=',$request->type == 'paid' ? 1 : 0)->where('featured','=', '1')->paginate($request->limit ?? 10);
            }
            else{

              $courses = $cats->courses()->where('status', '1')->where('featured','=', '1')->paginate($request->limit ?? 10);

            }

            


          }
          
        }else if($request->limit){

          // return 'ghjj';

          if($request->limit == '10'){

            $courses = $cats->courses()->where('status', '1')->paginate(2);

          }
          elseif($request->limit == '30'){

            $courses = $cats->courses()->where('status', '1')->paginate($request->limit);

          }
          elseif($request->limit == '50'){

            $courses = $cats->courses()->where('status', '1')->paginate($request->limit);

          }
          elseif($request->limit == '100'){

            $courses = $cats->courses()->where('status', '1')->paginate($request->limit);

          }
          else{

            $courses = $cats->courses()->where('status', '1')->paginate($request->limit ?? 10);

          }

        }

        else if($request->lang){

          $lang = CourseLanguage::where('id', $request->lang)->first();

          $courses = $cats->courses()->where('status', '1')->where('language_id','=',$lang->id)->paginate($request->limit ?? 10);

        }

        else{

          $courses = $cats->courses()->where('status', '1')->paginate($request->limit ?? 10);

        }

        $filter_count = $courses->count();

        
       
        return view('front.category',compact('cats', 'courses', 'filter_count', 'usercountry'));
       

    }


    public function reposition(Request $request)
    {

        $data= $request->all();
        
        $posts = Categories::all();
        $pos = $data['id'];
       
        $position =json_encode($data);
     
        foreach ($posts as $key => $item) {
            
            Categories::where('id', $item->id)->update(array('position' => $pos[$key]));
        }

        return response()->json(['msg'=>'Updated Successfully', 'success'=>true]);


    }
   

    public function catstatus(Request $request)
    {
        $catstatus = Categories::find($request->id);
        $catstatus->status = $request->status;
        $catstatus->save();
        return back()->with('success','Status change successfully.');
    }


    public function catfeatured(Request $request)
    {
        $catfeature = Categories::find($request->id);
        $catfeature->featured = $request->featured;
        $catfeature->save();
        return back()->with('success','Status change successfully.');
    }
}
