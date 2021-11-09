{{-- Extends layout --}}
@extends('admin.layout.default')

{{-- Content --}}
@section('content')

    <div class="card card-custom productpage">
        @if ($success = Session::get('success'))
        <div class="alert alert-success alert-block">
            <button type="button" class="close" data-dismiss="alert">×</button>    
            <strong>{{ $success }}</strong>
        </div>
        @endif
        @if ($error = Session::get('error'))
        <div class="alert alert-danger alert-block">
            <button type="button" class="close" data-dismiss="alert">×</button>    
            <strong>{{ $error }}</strong>
        </div>
        @endif


        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#tab1">Product Details</a></li>
            <li><a data-toggle="tab" href="#tab2">Gallery</a></li>
           {{--  @if(isset($products_attr_var) && ((@$products['product_type'] == 'variable'))) --}}
            <li><a data-toggle="tab" href="#tab3">Attributes</a></li>
            <li><a data-toggle="tab" href="#tab3A">Variations</a></li>
         {{--    @endif --}}
            <li><a data-toggle="tab" href="#tab4">Tech Documents</a></li>
            <li><a data-toggle="tab" href="#tab5">360 Images</a></li>
            <li><a data-toggle="tab" href="#tab6">Customer Reviews</a></li>
            <li><a data-toggle="tab" href="#tab7">FAQs</a></li>
            <li><a data-toggle="tab" href="#tab8">Training Videos</a></li>
            <li><a data-toggle="tab" href="#tab9">Feature Videos</a></li>
            <li><a data-toggle="tab" href="#tab10">Query Groups</a></li>
        </ul>
        <div class="tab-content">
            <!--------------------------------------------------------------------
            ----------- Set the all basic fields of products (tab1)------------------
            ---------------------------------------------------------------------->
            <div id="tab1" class="tab-pane fade in active show">
                <form class="form" method="post"
                      action="{{ URL::to(isset($products) ? '/admin/products/update' : '/admin/products/create')}}"
                      enctype="multipart/form-data">
                    {{csrf_field()}}
                    <input type="hidden" class="form-control form-control-solid" name="id"
                           value="{{@$products['id']}}"/>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Product Name:</label>
                            <input type="text" class="form-control form-control-solid" name="product_name"
                                   placeholder="Enter product name"
                                   value="{{ @$products['product_name'] ? @$products['product_name'] : old('product_name') }}"/>
                        </div>
                        @if(isset($products))
                            <div class="form-group">
                                <label>Slug:</label>
                                <input type="text" class="form-control form-control-solid" name="slug"
                                       placeholder="Enter slug name"
                                       value="{{ @$products['slug'] ? @$products['slug'] : old('slug') }}"/>
                            </div>
                        @endif
                        <div class="form-group">
                            <label>Product Type:</label>
                            <select class="form-control select2" id="product_type" name="product_type">
                                <option value="simple" {{ 'simple' == @$products['product_type'] ? "selected" : "" }}>Simple Product</option>
                                <option value="variable" {{ 'variable' == @$products['product_type'] ? "selected" : "" }}>Variable Product</option>
                              {{--   <option value="grouped">grouped</option>  --}}
                               
                            </select>
                        </div>
                        <div class="form-group">
                            @php                                
                                $productCategories = (isset($products) && $products) ? $products->productCategories->toArray() : [];
                                if(isset($productCategories)) {
                                    $productCategoryIds = @array_column($productCategories, 'category_id');
                                }
                            @endphp                           
                            
                            <label>Category: <span style="color:red">*</span></label>
                            <select class="form-control select2" multiple id="category_id" name="category_id[]">
                                {{-- <option value="">Select</option> --}}                             
                            @php
                                
                                $edit_cat =  ( isset($products['category_id'])) ? $products['category_id'] : "";
                        foreach ($category as $cat) {
                                echo '<option value="'.$cat->id.'" '.(($cat->id == $edit_cat) ? 'selected="selected"': "" ).'>'.$cat->category_name.'</option>';
                                if(isset($cat->childCategoires) && !empty($cat->childCategoires)){
                                    foreach ($cat->childCategoires as $aa) {
                                        echo '<option value="'.$aa->id.'" '.(($aa->id == $edit_cat) ? 'selected="selected"': "" ).'> - '.$aa->category_name.'</option>';
                                        if(isset($aa->childCategoires) && !empty($aa->childCategoires)){
                                            foreach ($aa->childCategoires as $bb) {
                                                echo '<option value="'.$bb->id.'" '.(($bb->id == $edit_cat) ? 'selected="selected"': "" ).'> - - '.$bb->category_name.'</option>';
                                                if(isset($bb->childCategoires) && !empty($bb->childCategoires)){
                                                    foreach ($bb->childCategoires as $cc) {
                                                        echo '<option value="'.$cc->id.'" '.(($cc->id == $edit_cat) ? 'selected="selected"': "" ).'> - - - '.$cc->category_name.'</option>';
                                                        if(isset($cc->childCategoires) && !empty($cc->childCategoires)){
                                                            foreach ($cc->childCategoires as $dd) {
                                                                echo '<option value="'.$dd->id.'" '.(($dd->id == $edit_cat) ? 'selected="selected"': "" ).'> - - - - '.$dd->category_name.'</option>';
                                                        
                                                            } 
                                                        }
                                                    } 
                                                }
                                            } 
                                        }
                                     } 
                                }
                            
                            }  

                              /*   foreach ($category as $cat) {
                               
                                 echo '<option value="'.$cat->id.'" '.(( isset($products['category_id']) && ($cat->id == $products['category_id']))?'selected="selected"':"").'>'.$cat->category_name.'</option>';
                                    if(isset($cat->childCategoires) && !empty($cat->childCategoires)){
                                    foreach ($cat->childCategoires as $ss) {
                                        echo '<option value="'.$ss->id.'" '.(( isset($products['category_id']) && ($ss->id == $products['category_id']))?'selected="selected"':"").'> - '.$ss->category_name.'</option>';
                                     
                                 
                                        if(isset($ss->childCategoires) && !empty($ss->childCategoires)){
                                        foreach ($ss->childCategoires as $dd) {
                                            echo '<option value="'.$dd->id.'" '.(( isset($products['category_id']) && ($dd->id == $products['category_id']))?'selected="selected"':"").'> - - '.$dd->category_name.'</option>';
                                         
                                        
                                        } 
                                    }
                                    } 
                                   }
                                   
                                } */
                            @endphp 
                             
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Brand:</label>
                            <select class="form-control select2" id="brand_id" name="brand_id">
                                <option value="">Select</option>
                                @foreach($brand as $isbrand)
                                    <option
                                        value="{{$isbrand->id}}" {{ $isbrand->id == @$products['brand_id'] ? "selected" : "" }}>{{$isbrand->brand_name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Product Main Image:</label>
                            @if(isset($products))
                            @if($products->main_image)
                                <img src="{{ URL::to(@$products->main_image) }}" alt="Product Image"
                                     width="50"
                                     height="50"/>
                                     @endif
                            @endif
                            <input type="file" class="form-control form-control-solid" name="main_image"
                                   accept="image/*"/>
                            <input type="hidden" class="form-control form-control-solid" name="old_main_image"
                                   value="{{ old('main_image',@$products->main_image) }}" accept="image/*"/>
                        </div>
                        <div class="form-group">
                            <label>SKU:</label>
                            <input type="text" class="form-control form-control-solid" name="sku"
                                   placeholder="Enter sku" value="{{ @$products['sku'] ? @$products['sku'] : old('sku') }}"/>
                        </div>
                        <div class="form-group">
                            <label>Regular Price:</label>
                            <input type="text" class="form-control form-control-solid" name="regular_price"
                                   placeholder="Enter regular price"
                                   value="{{ @$products['regular_price'] ? @$products['regular_price'] : old('regular_price') }}"/>
                        </div>
                        <div class="form-group">
                            <label>Sale Price:</label>
                            <input type="text" class="form-control form-control-solid" name="sale_price"
                                   placeholder="Enter sale price"
                                   value="{{ @$products['sale_price'] ? @$products['sale_price'] : old('sale_price') }}"/>
                        </div>
                       
                        <div class="form-group">
                            <label>Inventory:</label>
                            <input type="text" class="form-control form-control-solid" name="inventory"
                                   placeholder="Enter inventory"
                                   value="{{ @$products['inventory'] ? @$products['inventory'] : old('inventory') }}"/>
                        </div>
                        <div class="form-group">
                            <label>Product Description:</label>
                            <textarea id="description"
                                      name="description">{{ @$products['description'] ? @$products['description'] : old('description') }}</textarea>
                        </div>
                        <div class="form-group">
                            <label>Product Short Description:</label>
                            <textarea id="short_description"
                                      name="short_description">{{ @$products['short_description'] ? @$products['short_description'] : old('short_description') }}</textarea>
                        </div>
                        <div class="form-group">
                            <label>Product Specification:</label>
                            <textarea id="specification"
                                      name="specification">{{ @$products['specification'] ? @$products['specification'] : old('specification') }}</textarea>
                        </div>
                        <div class="form-group">
                            <label>Product Video:</label>
                            @if(isset($products))
                                <video width="100" height="100" controls>
                                    <source src="{{ URL::to(@$products->video) }}"
                                            type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            @endif
                            <input type="file" class="form-control form-control-solid" name="video"
                                   accept="video/*"/>
                            <input type="hidden" class="form-control form-control-solid" name="old_video"
                                   value="{{ old('video',@$products->video) }}" accept="video/*"/>
                        </div>
                        <div class="form-group">
                            <label>Download Datasheet:</label>
                            @if(isset($products))
                                <a href="{{ URL::to(@$products->download_datasheet) }}">View
                                    Datasheet</a>
                            @endif
                            <input type="file" class="form-control form-control-solid" name="download_datasheet"/>
                            <input type="hidden" class="form-control form-control-solid"
                                   name="old_download_datasheet"
                                   value="{{ old('download_datasheet',@$products->download_datasheet) }}"/>
                        </div>
                        <div class="form-group">
                            <label>SEO Name:</label>
                            <input type="text" class="form-control form-control-solid" name="seo_name"
                                   placeholder="Enter seo name"
                                   value="{{ @$products['seo_name'] ? @$products['seo_name'] : old('seo_name') }}"/>
                        </div>
                        <div class="form-group">
                            <label>SEO Description:</label>
                            <input type="text" class="form-control form-control-solid" name="seo_description"
                                   placeholder="Enter seo description"
                                   value="{{ @$products['seo_description'] ? @$products['seo_description'] : old('seo_description') }}"/>
                        </div>
                        <div class="form-group">
                            <label>SEO Title:</label>
                            <input type="text" class="form-control form-control-solid" name="seo_title"
                                   placeholder="Enter seo title"
                                   value="{{ @$products['seo_title'] ? @$products['seo_title'] : old('seo_title') }}"/>
                        </div>
                        <div class="form-group">
                            <label>SEO Keyword:</label>
                            <input type="text" class="form-control form-control-solid" name="seo_keyword"
                                   placeholder="Enter seo keyword"
                                   value="{{ @$products['seo_keyword'] ? @$products['seo_keyword'] : old('seo_keyword') }}"/>
                        </div>
                        <div class="form-group">
                            <label>Status:</label>
                            <select class="form-control" name="status">
                            <!--<option value="1" {{--{{ @$products['status'] == 1 ? "selected" : "" }}--}}>Active</option>
                                <option value="0" {{--{{ @$products['status'] == 0 ? "selected" : "" }}--}}>Inactive</option>-->
                                <option value="1" @if((old('status', $products->status ?? null ) == '1' ))
                                selected="selected" @endif>Active
                                </option>
                                <option value="0" @if((old('status', $products->status ?? null ) == '0' ))
                                selected="selected" @endif>Inactive
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit"
                                class="btn btn-primary mr-2">{{ isset($products) ? 'Update' : 'Add' }}</button>
                        <a class="btn btn-secondary" href="{{ route('products.index') }}">Cancel</a>
                    </div>
                </form>
            </div>
            <!--------------------------------------------------------------------
            ------------------- Set the gallery fields (Tab2)----------------------
            ---------------------------------------------------------------------->
            <div id="tab2" class="tab-pane fade">
                @if(isset($products))
                    <form class="form galleryform" method="post" action="{{ URL::to('/admin/products/updateGallery')}}"
                          enctype="multipart/form-data">
                        {{csrf_field()}}
                        <input type="hidden" class="form-control form-control-solid" name="id"
                               value="{{@$products['id']}}"
                        />
                        <div class="card-body">
                            <div class="form-group text-center">
                                <input type="file" id="galleryfiles" name="galleryfiles[]" multiple accept="image/*"
                                       class="inputfile">
                                <label for="galleryfiles"><i class="fa fa-upload"></i> Choose gallery images</label>
                            </div>
                            <div class="col-md-12">
                                @if(isset($products['gallery']) && !empty($products['gallery']))
                                    <div class="row draggable-zone" id="galleryimages">

                                        @foreach(explode(",",$products['gallery']) as $gallery)
                                            <div class="col-md-3 draggable text-center imgblock">
                                                <a href="#" class="draggable-handle">
                                                    <img width="100" height="100"
                                                         src="{{ URL::to($gallery)}}">
                                                </a>
                                                <a href="#" class="btn-danger deleteimg"><i
                                                        class="fa fa-remove"></i></a>
                                                <input type="hidden" name="galleryname[]" value="{{$gallery}}"></div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="row draggable-zone" id="galleryimages"></div>
                                @endif
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary mr-2">Add</button>
                            <button type="reset" class="btn btn-secondary">Cancel</button>
                        </div>
                    </form>
                @else
                    <p class="text-center pt-30 pb-30">Please add product basic information first.</p>
                @endif
            </div>
            <!--------------------------------------------------------------------
            ------------------- Set the attributes (Tab3)----------------------
            ---------------------------------------------------------------------->
            <div id="tab3" class="tab-pane fade">
                @if(isset($products))
                    <form class="form galleryform" method="post"
                          action="{{ URL::to('/admin/products/updateAttribute')}}"  >
                        {{csrf_field()}}
                        <input type="hidden" class="form-control form-control-solid" name="id"
                               value="{{@$products['id']}}"
                        />
                       {{--   {{ print_r(@$products_attributes)}} --}}
                        <div class="card-body">
                            <h3>Please select attributes and variations.</h3>
                            <div class="form-group">
                                <div class="checkbox-list">
                                    @foreach($attribute as $attr)
                                        <div class="parentattr">
                                            <label class="checkbox checkbox-lg">
                                                <input type="checkbox" id="attributes{{$attr['id']}}"
                                                       name="selattributes[]" class="form-control attributes"
                                                       value="{{$attr['id']}}" {{ in_array($attr['id'],$products_attributes_ids) ? "checked='checked'" : "" }}/><span></span>{{$attr['attribute_name']}}
                                            </label>
                                        </div>
                                        <div class="subattr">
                                            <div class="checkbox-inline variationlist">
                                                @foreach($variation as $attvar)
                                                @php
                                                $attr_ID = $attr['id'];
                                              
                                                    @endphp
                                                    @if($attvar['attribute_id'] == $attr['id'])
                                                        <label class="checkbox">
                                                            <input type="checkbox" id="attributes{{$attvar['id']}}"
                                                                   name="selvariations[{{$attr['id']}}][]"
                                                                   class="form-control attributes"
                                                                   value="{{$attvar['id']}}" {{ (isset($products_attributes_options[$attr_ID]) && in_array($attvar['id'], $products_attributes_options[$attr_ID])) ? "checked='checked'" : "" }}/><span></span>{{$attvar['variation_name']}}
                                                        </label>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary mr-2">Save</button>
                            <button type="reset" class="btn btn-secondary">Cancel</button>
                        </div>
                    </form>
                @else
                    <p class="text-center pt-30 pb-30">Please add product basic information first.</p>
                @endif
            </div>
            <!--------------------------------------------------------------------
            ------------------- Set the Variations (Tab3A)----------------------
            ---------------------------------------------------------------------->
            <div id="tab3A" class="tab-pane fade">
                @if(isset($products))
                @if(isset($products_attr_var) && ((@$products['product_type'] == 'variable')))
                <form class="form variantform" method="post"
                action="{{ URL::to('/admin/products/addProductVariant')}}"  >
                {{csrf_field()}}
                        <input type="hidden" class="form-control form-control-solid" name="product_id"
                               value="{{@$products['id']}}"
                        /> 
                <div class="card-body">
                    <div class="row">
                    @php
                    $numVariantArr = count($products_attr_var);
                    $variantArr = array();
                    $vi = 0;
                    $vi2 = 1;
                    @endphp
                        @foreach($products_attr_var as $attr)
                            @if (!in_array($attr->attribute_id, $variantArr)) 
                                @empty(!$variantArr)
                                
                                        </select>
                                    </div>
                                </div>
                                @endempty

                                <div class="col-md-3">
                                    <div class="form-group">  
                                        <label>Select {{ $attr->attribute_name }}:</label> 
                                         <select class="form-control select2"  id="attribute_select{{ $vi2  }}" name="variation_id{{ $vi2 }}">
                                            <option value="">Select</option>
                                        @endif
                                         @if (old('variation_id'.$vi2 ) == $attr->id)
                                            <option value="{{$attr->id}}"  selected >{{ $attr->variation_name }}
                                            </option>
                                        @else
                                            <option value="{{$attr->id}}" >{{ $attr->variation_name }}
                                            </option>

                                        @endif
                                         
                                    @if (!in_array($attr->attribute_id, $variantArr)) 
                                    @php
                                    $variantArr[] = $attr->attribute_id;
                                    $vi2++; 
                                    @endphp
                                       @endif

                                       @if(++$vi === $numVariantArr)  
                                        </select>
                                    </div>
                                 </div>
                                 @endif
                            
                        @endforeach
                  
                        <input  type="hidden" name="noOfVariation" value="{{ $vi2 - 1  }}"/>
                        <div class="col-md-2">
                            <div class="form-group"> 
                                <label>SKU:</label> 
                                <input  class="form-control form-control-solid" type="text" name="sku" value="{{  old('sku') }}"/>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group"> 
                                <label>Quantity:</label> 
                                <input  class="form-control form-control-solid" type="text" name="quantity" value="{{  old('quantity') }}"/>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group"> 
                                <button type="submit"
                                class="btn btn-primary mt-7">Add Variation</button>
                            </div>
                        </div>
                    </div>
                </div>
                </form>
                @endif

                @if(isset($products_variants))
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                <div class="accordion  accordion-toggle-arrow1" id="accordionExample2">
                    @foreach($products_variants as $variant)
                    <div class="card card{{ $variant->id }}">
                      <div class="card-header" id="headingOne{{ $variant->id}}">
                      <div class="card-title" data-toggle="collapse" data-target="#collapseOne{{ $variant->id }}">
                       {{ $variant->item_name }}  
                      </div>
                      <a href="javascript:;" data-url="{{ URL::to('/admin/products/deleteProductVariant/'.$variant->id)}}" data-message="Are you sure you want to delete {{ $variant->item_name }} ?"
                        data-success="The product variant has been deleted successfully."
                        class="btn btn-icon btn-light btn-hover-primary btn-sm deleteitem" title="Delete"><i class="fa fa-remove"></i></a>
                     
                     </div> 
                        <div id="collapseOne{{ $variant->id }}" class="collapse {{ session('variantId') ==  $variant->id ? 'show' : 'hide'  }}" data-parent="#accordionExample2">
                   
                      
                                           <div class="card-body">
                        <form class="form variantform" method="post"
                        action="{{ URL::to('/admin/products/updateProductVariant')}}"  >
                        {{csrf_field()}}

                        <input type="hidden" class="form-control form-control-solid" name="id"
                        value="{{ $variant->id }}"/>
                        <input type="hidden" class="form-control form-control-solid" name="product_id"
                            value="{{ $variant->product_id }}"/>
                            <div class="form-group">
                                <label>Item Name:</label>
                                <input type="text" class="form-control form-control-solid" name="item_name"
                                    placeholder="Enter variant name"
                                    value="{{ $variant->item_name }}"/>
                            </div>
                            <div class="form-group">
                                <label>Item Code:</label>
                                <input type="text" class="form-control form-control-solid" name="item_code"
                                    placeholder="Enter variant code"
                                    value="{{ $variant->item_code }}"/>
                            </div>
                            <div class="form-group">
                                <label>SKU:</label>
                                <input type="text" class="form-control form-control-solid" name="sku"
                                    placeholder="sku"
                                    value="{{ $variant->sku }}"/>
                            </div>
                            <div class="form-group">
                                <label>Quantity:</label>
                                <input type="text" class="form-control form-control-solid" name="Quantity"
                                    placeholder="Quantity"
                                    value="{{ $variant->OnHand }}"/>
                            </div>
                            <div class="form-group">
                                <label>Gross Weight:</label>
                                <input type="text" class="form-control form-control-solid" name="U_GrossWt"
                                    placeholder="Gross weight"
                                    value="{{ $variant->U_GrossWt }}"/>
                            </div>
                            <div class="form-group">
                                <label>Net Weight:</label>
                                <input type="text" class="form-control form-control-solid" name="U_NetWt"
                                    placeholder="Net Weight"
                                    value="{{ $variant->U_NetWt }}"/>
                            </div>
                            <div class="form-group"> 
                                <button type="submit"
                                class="btn btn-primary mr-2">Save</button>
                            </div>
                        </form>


                      
                      </div>
                     </div>
                    </div>
                    @endforeach
                    {{-- <div class="card">
                     <div class="card-header" id="headingTwo2">
                      <div class="card-title collapsed" data-toggle="collapse" data-target="#collapseTwo2">
                       Accounting Updates
                      </div>
                     </div>
                     <div id="collapseTwo2" class="collapse"  data-parent="#accordionExample2">
                      <div class="card-body">
                       ...
                      </div>
                     </div>
                    </div>
                    <div class="card">
                     <div class="card-header" id="headingThree2">
                      <div class="card-title collapsed" data-toggle="collapse" data-target="#collapseThree2">
                       Latest Payroll
                      </div>
                     </div>
                     <div id="collapseThree2" class="collapse" data-parent="#accordionExample2">
                      <div class="card-body">
                       ...
                      </div>
                     </div>
                    </div> --}}
                   </div>
                </div>
                </div>
                </div>

              
                      

                       
                
                @endif

                @else
                <p class="text-center pt-30 pb-30">Please add product basic information first.</p>
                 @endif
                 
            </div>

            <!--------------------------------------------------------------------
            ------------------- Set the tech document (Tab4)----------------------
            ---------------------------------------------------------------------->
            <div id="tab4" class="tab-pane fade">
            
                @if(isset($products))
                    <form class="form galleryform" method="post" action="{{ URL::to('/admin/products/updateTechDoc')}}"
                          enctype="multipart/form-data">
                        {{csrf_field()}}
                        <input type="hidden" class="form-control form-control-solid" name="id"
                               value="{{@$products['id']}}"
                        />
                        <div class="card-body">
                            <div class="form-group text-center">
                                <input type="file" id="techdocumentsfiles" name="techdocumentsfiles[]" multiple
                                       accept="*" class="inputfile">
                                <label for="techdocumentsfiles"><i class="fa fa-upload"></i> Choose Technical Documents</label>
                            </div>
                            <div class="col-md-12">
                            
                                @if(isset($products['tech_documents']) && !empty($products['tech_documents']))
                                    <div class="row draggable-zone" id="techdocuments">

                                        @foreach(explode(",",$products['tech_documents']) as $techdocuments)
                                            <div class="col-md-3 draggable text-center imgblock">
                                                <div class="docsblock draggable-handle"><a
                                                href="{{ URL::to($techdocuments)}}"
                                                        class=""><i class="fa fa-file"></i>{{$techdocuments}}</a></div>
                                                <a href="#" class="btn-danger deleteimg"><i
                                                        class="fa fa-remove"></i></a>
                                                <input type="hidden" name="techdocumentsname[]"
                                                       value="{{$techdocuments}}"></div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="row draggable-zone" id="techdocuments"></div>
                                @endif
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary mr-2">Add</button>
                            <button type="reset" class="btn btn-secondary">Cancel</button>
                        </div>
                    </form>
                @else
                    <p class="text-center pt-30 pb-30">Please add product basic information first.</p>
                @endif
            </div>
            <!--------------------------------------------------------------------
            ------------------- Set the 360 images (Tab5)----------------------
            ---------------------------------------------------------------------->
            <div id="tab5" class="tab-pane fade">
                @if(isset($products))
                    <form class="form galleryform" method="post"
                          action="{{ URL::to('/admin/products/updateThreeSixty')}}" enctype="multipart/form-data">
                        {{csrf_field()}}
                        <input type="hidden" class="form-control form-control-solid" name="id"
                               value="{{@$products['id']}}"
                        />
                        <div class="card-body">
                            <div class="form-group text-center">
                                <input type="file" id="threesixtyfiles" name="threesixtyfiles[]" multiple
                                       accept="image/*" class="inputfile">
                                <label for="threesixtyfiles"><i class="fa fa-upload"></i> Choose 360 images</label>
                            </div>
                            <div class="col-md-12">
                                @if(isset($products['threesixty_images']) && !empty($products['threesixty_images']))
                                    <div class="row draggable-zone" id="threesixtyimages">

                                        @foreach(explode(",",$products['threesixty_images']) as $img)
                                            <div class="col-md-3 draggable text-center imgblock">
                                                <a href="#" class="draggable-handle">
                                                    <img width="100" height="100"
                                                         src="{{ URL::to('uploads/products/threesixty/').'/'.$img}}">
                                                </a>
                                                <a href="#" class="btn-danger deleteimg"><i
                                                        class="fa fa-remove"></i></a>
                                                <input type="hidden" name="threesixtyname[]" value="{{$img}}"></div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="row draggable-zone" id="threesixtyimages"></div>
                                @endif
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary mr-2">Add</button>
                            <button type="reset" class="btn btn-secondary">Cancel</button>
                        </div>
                    </form>
                @else
                    <p class="text-center pt-30 pb-30">Please add product basic information first.</p>
                @endif
            </div>
            <!--------------------------------------------------------------------
            ---------- Set the customer reviews list (Tab6)-------------------
            ---------------------------------------------------------------------->
            <div id="tab6" class="tab-pane fade">
                @if(isset($products))
                    <div class="card-body">
                        <div class="datatable datatable-default datatable-bordered datatable-loaded">
                            <table class="table table-bordered table-hover " id="datatable">
                                <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Title</th>
                                    <th>Comment</th>
                                    <th>Rating</th>
                                    <th>Username</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($review as $key => $data)
                                    <tr>
                                        <td>{{ $key+1 }}</td>
                                        <td>{{ $data->title }}</td>
                                        <td>{{ $data->comment }}</td>
                                        <td>@for($i=1; $i<=$data->rating; $i++)
                                                <i class="fa fa-star"></i>
                                            @endfor
                                        </td>
                                        <td>{{ $data->first_name.' '.$data->last_name }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    <p class="text-center pt-30 pb-30">Please add product basic information first.</p>
                @endif
            </div>
            <!--------------------------------------------------------------------
            ---------- Set the faq page (Tab7)-------------------
            ---------------------------------------------------------------------->
            <div id="tab7" class="tab-pane fade">
                @if(isset($products))
                    <form class="form galleryform" method="post" action="{{ URL::to('/admin/products/faq/create')}}"
                          enctype="multipart/form-data">
                        {{csrf_field()}}
                        <input type="hidden" class="form-control form-control-solid" name="proid"
                               value="{{@$products['id']}}"
                        />
                        @if(!empty($faqs[0]['title']))
                            <div class="card-body">
                                <div class="card-header1 border-0 py-5">
                                    <h3 class="card-title align-items-start flex-column">
                                    </h3>
                                    <div class="card-toolbar">
                                        <a href="{{@$products['id']}}/faq/add"
                                           class="btn btn-primary font-weight-bolder font-size-sm">Add new</a>
                                    </div>
                                </div>
                                <table class="table table-bordered table-hover " id="datatable">
                                    <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Title</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($faqs as $keyno => $data)
                                        <tr>
                                            <td>{{ $keyno+1 }}</td>
                                            <td>{{ $data->title }}</td>
                                            <td>
                                                <a href="{{@$products['id']}}/faq/edit/{{$data->id}}"
                                                   class="btn btn-icon btn-light btn-hover-primary btn-sm"><i
                                                        class="fa fa-pencil"></i></a>
                                                <a href="javascript:;"
                                                   data-url="{{@$products['id']}}/faq/delete/{{$data->id}}"
                                                   class="btn btn-icon btn-light btn-hover-primary btn-sm deleteitem"><i
                                                        class="fa fa-remove"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="card-body">
                                <div id="faqblocks">
                                    <div id="fblocks">
                                        <p class="faqmaintitle">FAQ</p>
                                        <div class="form-group">
                                            <label for="title">FAQ Title</label>
                                            <input type="text" id="title" name="title" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label for="content">FAQ Content</label>
                                            <textarea   name="description" class="form-control"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary mr-2">Add</button>
                                <button type="reset" class="btn btn-secondary">Cancel</button>
                            </div>
                        @endif
                    </form>
                @else
                    <p class="text-center pt-30 pb-30">Please add product basic information first.</p>
                @endif
            </div>
            <!--------------------------------------------------------------------
            ------------------- Set the Training videos (Tab8)----------------------
            ---------------------------------------------------------------------->
            <div id="tab8" class="tab-pane fade">
                @if(isset($products))
                    <form class="form galleryform" method="post"
                          action="{{ URL::to('/admin/products/trainingvideo/create')}}" enctype="multipart/form-data">
                        {{csrf_field()}}
                        <input type="hidden" class="form-control form-control-solid" name="proid"
                               value="{{@$products['id']}}"
                        />
                        @if(!empty($training_videos[0]['video']))
                            <div class="card-body">
                                <div class="card-header1 border-0 py-5">
                                    <h3 class="card-title align-items-start flex-column">
                                    </h3>
                                    <div class="card-toolbar">
                                        <a href="{{@$products['id']}}/trainingvideo/add"
                                           class="btn btn-primary font-weight-bolder font-size-sm">Add new</a>
                                    </div>
                                </div>
                                <table class="table table-bordered table-hover " id="datatable">
                                    <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Name</th>
                                        <th>Video</th>
                                        <th>Display order</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($training_videos as $keyno => $data)
                                        <tr>
                                            <td>{{ $keyno+1 }}</td>
                                            <td>{{ $data->name }}</td>
                                            <td>
                                                <video width="100" height="100" controls>
                                                    <source
                                                    src="{{ URL::to($data->video)}}"
                                                        type="video/mp4">
                                                    Your browser does not support the video tag.
                                                </video>
                                            </td>
                                            <td>{{ $data->display_order }}</td>
                                            <td>
                                                <a href="{{@$products['id']}}/trainingvideo/edit/{{$data->id}}"
                                                   class="btn btn-icon btn-light btn-hover-primary btn-sm"><i
                                                        class="fa fa-pencil"></i></a>
                                                <a href="javascript:;"
                                                   data-url="{{@$products['id']}}/trainingvideo/delete/{{$data->id}}"
                                                   class="btn btn-icon btn-light btn-hover-primary btn-sm deleteitem"><i
                                                        class="fa fa-remove"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="trainingvideoname">Training video name</label>
                                    <input type="text" id="trainingvideoname" name="trainingvideoname"
                                           class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="trainingvideofiles">Select training video</label>
                                    <input type="file" id="trainingvideofiles" name="trainingvideofiles" multiple
                                           accept="video/*" class="form-control1">
                                </div>
                                <div class="form-group">
                                    <label for="trainingvideoname">Display Order</label>
                                    <input type="text" id="display_order" name="display_order" class="form-control">
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary mr-2">Add</button>
                                <button type="reset" class="btn btn-secondary">Cancel</button>
                            </div>
                        @endif
                    </form>
                @else
                    <p class="text-center pt-30 pb-30">Please add product basic information first.</p>
                @endif
            </div>
            <!--------------------------------------------------------------------
            ------------------- Set the Feature videos (Tab9)----------------------
            ---------------------------------------------------------------------->
            <div id="tab9" class="tab-pane fade">
                @if(isset($products))
                    <form class="form galleryform" method="post"
                          action="{{ URL::to('/admin/products/featurevideo/create')}}" enctype="multipart/form-data">
                        {{csrf_field()}}
                        <input type="hidden" class="form-control form-control-solid" name="proid"
                               value="{{@$products['id']}}"
                        />
                        @if(!empty($feature_videos[0]['video']))
                            <div class="card-body">
                                <div class="card-header1 border-0 py-5">
                                    <h3 class="card-title align-items-start flex-column">
                                    </h3>
                                    <div class="card-toolbar">
                                        <a href="{{@$products['id']}}/featurevideo/add"
                                           class="btn btn-primary font-weight-bolder font-size-sm">Add new</a>
                                    </div>
                                </div>
                                <table class="table table-bordered table-hover " id="datatable">
                                    <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Name</th>
                                        <th>Video</th>
                                        <th>Display order</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($feature_videos as $keyno => $data)
                                        <tr>
                                            <td>{{ $keyno+1 }}</td>
                                            <td>{{ $data->name }}</td>
                                            <td>
                                                <video width="100" height="100" controls>
                                                    <source
                                                        src="{{ URL::to($data->video)}}"
                                                        type="video/mp4">
                                                    Your browser does not support the video tag.
                                                </video>
                                            </td>
                                            <td>{{ $data->display_order }}</td>
                                            <td>
                                                <a href="{{@$products['id']}}/featurevideo/edit/{{$data->id}}"
                                                   class="btn btn-icon btn-light btn-hover-primary btn-sm"><i
                                                        class="fa fa-pencil"></i></a>
                                                <a href="javascript:;"
                                                   data-url="{{@$products['id']}}/featurevideo/delete/{{$data->id}}"
                                                   class="btn btn-icon btn-light btn-hover-primary btn-sm deleteitem"><i
                                                        class="fa fa-remove"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="featurevideoname">Feature video name</label>
                                    <input type="text" id="featurevideoname" name="featurevideoname"
                                           class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="featurevideofiles">Select feature video</label>
                                    <input type="file" id="featurevideofiles" name="featurevideofiles" multiple
                                           accept="video/*" class="form-control1">
                                </div>
                                <div class="form-group">
                                    <label for="featurevideoname">Display Order</label>
                                    <input type="text" id="display_order" name="display_order" class="form-control">
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary mr-2">Add</button>
                                <button type="reset" class="btn btn-secondary">Cancel</button>
                            </div>
                        @endif
                    </form>
                @else
                    <p class="text-center pt-30 pb-30">Please add product basic information first.</p>
                @endif
            </div>
            <!-----------------------------------------------------
            ------------------- Set the Feature videos (Tab10)----------------------
            ---------------------------------------------------------------------->
            <div id="tab10" class="tab-pane fade">
                
                <div class="card-body">
                    @if(isset($products))
                    <div class="col-lg-12">
                        <h3 class="mb-5">Query group parameter</h3>
                    </div >
                       <div class="row">
                            
                            @for ($qi = 1; $qi <= 64; $qi++ )
                                @php
                                $checked = '';
                                $quaryName = 'QryGroup';
                                $quaryName .= $qi;
                                $quaryValue  = $products->$quaryName;
                                if($quaryValue == 'Y'){
                                    $checked = 'checked=checked'; 
                                }
                                @endphp
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <div class="checkbox-list">
                                            <label class="checkbox" disabled readonly>
                                            <input   type="checkbox" {{$checked}} disabled  readonly>
                                            <span class="ml-3"></span>{{$quaryName}}</label>
                                        </div>
                                    </div>
                                </div>  
                            @endfor
                           
                        </div>
                        @else
                         <p class="text-center pt-30 pb-30">Please add product basic information first.</p>
                         @endif
                    </div>
                    
            </div>
        </div>
       

@endsection
{{-- Styles Section --}}
@section('styles')
    <link href="{{ asset('plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css"/>
    <style>
        .accordion .deleteitem{
           position: absolute;
           right: 10px;
           top: 7px;
       }
       .accordion .card .card-header .card-title[aria-expanded="true"] {
            background: #32c5d2;
            color: #fff;
        }
       .variantform .form-group label{
            display: block;
       }
       </style>
@endsection
{{-- Scripts Section --}}
@section('scripts')
    <!--begin::Page Vendors(used by this page)-->
    <script src="{{ asset('plugins/custom/datatables/datatables.bundle.js') }}" type="text/javascript"></script>
    <script type="text/javascript">
        $('#datatable').DataTable({responsive: true});
    </script>
    <script src="{{ asset('plugins/custom/ckeditor/ckeditor-classic.bundle.js?v=7.1.7') }}"></script>
    <!--end::Page Vendors-->
    <!--begin::Page Scripts(used by this page)-->
    <script type="text/javascript">
        var KTCkeditor = function () {
            // Private functions
            var demos = function () {
                ClassicEditor
                    .create(document.querySelector('#description'))
                    .then(editor => {
                        console.log(editor);
                    })
                    .catch(error => {
                        console.error(error);
                    });
            }
            var demos1 = function () {
                ClassicEditor
                    .create(document.querySelector('#short_description'))
                    .then(editor => {
                        console.log(editor);
                    })
                    .catch(error => {
                        console.error(error);
                    });
            }
            var demos2 = function () {
                ClassicEditor
                    .create(document.querySelector('#specification'))
                    .then(editor => {
                        console.log(editor);
                    })
                    .catch(error => {
                        console.error(error);
                    });
            }
            return {
                // public functions
                init: function () {
                    demos();
                    demos1();
                    demos2();
                }
            };
        }();

        // Initialization
        jQuery(document).ready(function () {
            KTCkeditor.init();
        });
        
        $('#product_type').select2({
            placeholder: "Select product type"
        });

        var updatedDataArr = @json($productCategoryIds);        
        $('#category_id').val(updatedDataArr);
        $('#category_id').select2({
            placeholder: "Select category",
        });

        $('#brand_id').select2({
            placeholder: "Select brand"
        });
        $('#attribute_select1').select2({
            placeholder: "Select a Option"
        });
        $('#attribute_select2').select2({
            placeholder: "Select a Option"
        });
        $('#attribute_select3').select2({
            placeholder: "Select a Option"
        });

    </script>
    <script src="{{ asset('/js/ondeletepopupporduct.js') }}"></script>
    @if(isset($products))
    <script>
        /*------------------------------------------------------------------------ --------
          set the code for file multiple upload and show images on dragable section.(gallery)
        -------------------------------------------------------------------------------*/
        var selDiv = "";
        document.addEventListener("DOMContentLoaded", init, false);

        function init() {
            document.querySelector('#galleryfiles').addEventListener('change', handleFileSelect, false);
            selDiv = document.querySelector("#galleryimages");
        }

        function handleFileSelect(e) {
            if (!e.target.files || !window.FileReader) return;
            //selDiv.innerHTML = "";
            var files = e.target.files;
            var filesArr = Array.prototype.slice.call(files);
            filesArr.forEach(function (f) {
                //var f = files[i];
                if (!f.type.match("image.*")) {
                    return;
                }
                var reader = new FileReader();
                reader.onload = function (e) {
                    var html = "<div class='col-md-3 draggable text-center imgblock'><a href='#' class='draggable-handle'><img width='100' height='100' src=\"" + e.target.result + "\"></a><a href='#' class='btn-danger deleteimg'><i class='fa fa-remove'></i></a><input type='hidden' name='galleryname[]' value='" + f.name + "'></div>";
                    selDiv.innerHTML += html;
                }
                reader.readAsDataURL(f);
            });
        }

        /*--------------------------------------------------------------------------------
           Technical document
         ---------------------------------------------------------------------------------*/
        var docDiv = "";
        document.addEventListener("DOMContentLoaded", inittechdoc, false);

        function inittechdoc() {
            document.querySelector('#techdocumentsfiles').addEventListener('change', handletechdoc, false);
            docDiv = document.querySelector("#techdocuments");
        }

        function handletechdoc(e) {
            if (!e.target.files || !window.FileReader) return;
            //docDiv.innerHTML = "";
            var files = e.target.files;
            for (var i = 0; i < files.length; i++) {
                var f = files[i];
                docDiv.innerHTML += "<div class='col-md-3 draggable text-center imgblock'><div class='docsblock draggable-handle'><a href='#' class=''><i class='fa fa-file'></i>" + f.name + "</a></div><a href='#' class='btn-danger deleteimg'><i class='fa fa-remove'></i></a><input type='hidden' name='techdocumentsname[]' value='" + f.name + "'></div>";
            }
        }

        /*------------------------------------------------------------------------ --------
          360 Images
        -------------------------------------------------------------------------------*/
        var threesixtyDiv = "";
        document.addEventListener("DOMContentLoaded", threesixtyinit, false);

        function threesixtyinit() {
            document.querySelector('#threesixtyfiles').addEventListener('change', handlethreesixty, false);
            threesixtyDiv = document.querySelector("#threesixtyimages");
        }

        function handlethreesixty(e) {
            if (!e.target.files || !window.FileReader) return;
            //threesixtyDiv.innerHTML = "";
            var files = e.target.files;
            var filesArr = Array.prototype.slice.call(files);
            filesArr.forEach(function (f) {
                //var f = files[i];
                if (!f.type.match("image.*")) {
                    return;
                }
                var reader = new FileReader();
                reader.onload = function (e) {
                    var html = "<div class='col-md-3 draggable text-center imgblock'><a href='#' class='draggable-handle'><img width='100' height='100' src=\"" + e.target.result + "\"></a><a href='#' class='btn-danger deleteimg'><i class='fa fa-remove'></i></a><input type='hidden' name='threesixtyname[]' value='" + f.name + "'></div>";
                    threesixtyDiv.innerHTML += html;
                }
                reader.readAsDataURL(f);
            });
        }

        /*-------------------------------------------------------*/
        $(document).on('click', '.deleteimg', function () {
            $(this).parent().remove();
        });
    </script>
    @endif
    @if(request()->tab)
        <script type="text/javascript">
            var tabno = "{{request()->tab}}";
            var tabvalue = "#tab" + "{{request()->tab}}";
            console.log('sdsad');
            $(".tab-content .tab-pane").removeClass("in active show");
            $(tabvalue).addClass("in active show");
            $(".nav-tabs li").removeClass("active");
            $(".nav-tabs").children('li').eq(tabno - 1).addClass("active");
        </script>
    @endif
    <script src="{{ asset('plugins/custom/draggable/draggable.bundle.js') }}"></script>
    <script src="{{ asset('js/pages/features/cards/draggable.js') }}"></script>
    <!--end::Page Scripts-->
    <style>
        .slug {
            display: none;
        }
    </style>
@endsection
