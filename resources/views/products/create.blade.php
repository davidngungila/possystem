@extends('layouts.admin')
@section('title','Add Product')
@section('content')
<div class="page-head">
    <div><h1>Add Product</h1><p class="page-sub">SKU = internal code · Barcode = scanned code · Barcode unique guard</p></div>
    <a href="{{ route('products.index') }}" class="btn btn-ghost btn-sm">Back to Products</a>
</div>

<div class="panel">
    <div class="panel-body">
        <div style="margin-bottom:18px;padding:14px 16px;background:var(--sand-50);border:1.5px solid var(--line);border-radius:12px">
            <label class="field-label" style="margin-bottom:6px">Search from Sample Catalogue (damp) — type to autofill</label>
            <div style="position:relative">
                <input id="catalogueSearch" placeholder="Search product name, e.g. Sugar, Paracetamol, Cement..." autocomplete="off" style="width:100%;padding:10px 12px 10px 36px;border:1.5px solid var(--line);border-radius:10px;background:#fff url('data:image/svg+xml;utf8,<svg xmlns=&quot;http://www.w3.org/2000/svg&quot; viewBox=&quot;0 0 24 24&quot; fill=&quot;none&quot; stroke=&quot;%2340637a&quot; stroke-width=&quot;2&quot;><circle cx=&quot;11&quot; cy=&quot;11&quot; r=&quot;8&quot;/><line x1=&quot;21&quot; y1=&quot;21&quot; x2=&quot;16.65&quot; y2=&quot;16.65&quot;/></svg>') no-repeat 10px center / 16px 16px;">
                <div id="catalogueResults" style="position:absolute;top:100%;left:0;right:0;background:#fff;border:1px solid var(--line);border-radius:10px;box-shadow:0 8px 24px rgba(16,36,48,.12);max-height:260px;overflow:auto;display:none;z-index:20;margin-top:6px"></div>
            </div>
            <span class="field-hint">350+ demo products — selecting autofills category, unit, tax, etc. Edit before save. SKU will be auto-generated.</span>
        </div>
        <form method="POST" action="{{ route('products.store') }}">
            @csrf
            <div class="form-grid">
                <div class="field @error('name') err @enderror">
                    <label class="field-label">Product Name *</label>
                    <input name="name" value="{{ old('name') }}" required placeholder="e.g. Coca Cola 500ml">
                    @error('name')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field @error('sku') err @enderror">
                    <label class="field-label">SKU * <span style="text-transform:none;font-weight:600;color:var(--ink-soft)">internal</span></label>
                    <input name="sku" value="{{ old('sku') }}" required placeholder="e.g. COCA-500">
                    @error('sku')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field @error('barcode') err @enderror">
                    <label class="field-label">Barcode <span style="text-transform:none;font-weight:600;color:var(--ink-soft)">scanned - leave empty if none</span></label>
                    <input name="barcode" value="{{ old('barcode') }}" placeholder="e.g. 1234567890123 or 2510VFD090007">
                    @error('barcode')<span class="field-err">{{ $message }}</span>@enderror
                    <span class="field-hint">Supports EAN-13, EAN-8, UPC, Code128, Code39, QR, Internal. Duplicate blocked.</span>
                </div>
                <div class="field @error('category_id') err @enderror">
                    <label class="field-label">Category</label>
                    <select name="category_id">
                        <option value="">— None —</option>
                        @foreach($categories as $c)<option value="{{ $c->id }}" @selected(old('category_id')==$c->id)>{{ $c->name }}</option>@endforeach
                    </select>
                    @error('category_id')<span class="field-err">{{ $message }}</span>@enderror
                    @if(isset($currentShop) && $currentShop && !empty($currentShop->shopTypesArray()))
                        <span class="field-hint">Filtered for {{ $currentShop->shopTypeLabel() }} — {{ $categories->count() }} of {{ \App\Models\Category::count() }} categories (assigned to this shop type)</span>
                    @else
                        <span class="field-hint">All categories — assign categories to shop types via Categories → Edit</span>
                    @endif
                </div>
                <div class="field @error('brand_id') err @enderror">
                    <label class="field-label">Brand</label>
                    <select name="brand_id">
                        <option value="">— None —</option>
                        @foreach($brands as $b)<option value="{{ $b->id }}" @selected(old('brand_id')==$b->id)>{{ $b->name }}</option>@endforeach
                    </select>
                </div>
                <div class="field @error('unit_id') err @enderror">
                    <label class="field-label">Unit</label>
                    <select name="unit_id">
                        <option value="">— None —</option>
                        @foreach($units as $u)<option value="{{ $u->id }}" @selected(old('unit_id')==$u->id)>{{ $u->name }} @if($u->short_name) ({{ $u->short_name }}) @endif</option>@endforeach
                    </select>
                </div>
                <div class="field @error('supplier_id') err @enderror">
                    <label class="field-label">Supplier</label>
                    <select name="supplier_id">
                        <option value="">— None —</option>
                        @foreach($suppliers as $s)<option value="{{ $s->id }}" @selected(old('supplier_id')==$s->id)>{{ $s->name }}</option>@endforeach
                    </select>
                </div>
                <div class="field @error('buying_price') err @enderror">
                    <label class="field-label">Buying Price *</label>
                    <input name="buying_price" type="number" step="0.01" value="{{ old('buying_price',0) }}" required>
                    @error('buying_price')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field @error('selling_price') err @enderror">
                    <label class="field-label">Selling Price *</label>
                    <input name="selling_price" type="number" step="0.01" value="{{ old('selling_price',0) }}" required>
                    @error('selling_price')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field @error('wholesale_price') err @enderror">
                    <label class="field-label">Wholesale Price</label>
                    <input name="wholesale_price" type="number" step="0.01" value="{{ old('wholesale_price') }}" placeholder="optional">
                </div>
                <div class="field @error('current_stock') err @enderror">
                    <label class="field-label">Current Stock *</label>
                    <input name="current_stock" type="number" value="{{ old('current_stock',0) }}" required>
                    @error('current_stock')<span class="field-err">{{ $message }}</span>@enderror
                    <span class="field-hint">Initial stock creates stock movement IN</span>
                </div>
                <div class="field @error('min_stock') err @enderror">
                    <label class="field-label">Minimum Stock *</label>
                    <input name="min_stock" type="number" value="{{ old('min_stock',5) }}" required>
                </div>
                <div class="field @error('tax_rate') err @enderror">
                    <label class="field-label">Tax %</label>
                    <input name="tax_rate" type="number" step="0.01" value="{{ old('tax_rate',0) }}">
                </div>
                <div class="field @error('expiry_date') err @enderror">
                    <label class="field-label">Expiry Date</label>
                    <input name="expiry_date" type="date" value="{{ old('expiry_date') }}">
                </div>
                <div class="field @error('batch_number') err @enderror">
                    <label class="field-label">Batch Number</label>
                    <input name="batch_number" value="{{ old('batch_number') }}" placeholder="optional">
                </div>
                <div class="field @error('status') err @enderror">
                    <label class="field-label">Status *</label>
                    <select name="status" required>
                        <option value="active" @selected(old('status','active')==='active')>Active</option>
                        <option value="inactive" @selected(old('status')==='inactive')>Inactive</option>
                        <option value="discontinued" @selected(old('status')==='discontinued')>Discontinued</option>
                    </select>
                </div>
            </div>
            <div class="field" style="margin-top:16px">
                <label class="field-label">Description</label>
                <textarea name="description" placeholder="optional">{{ old('description') }}</textarea>
            </div>

            <div class="form-actions">
                <a href="{{ route('products.index') }}" class="btn btn-ghost">Cancel</a>
                <button class="btn btn-primary">Save Product</button>
            </div>
        </form>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function(){
  const input = document.getElementById('catalogueSearch');
  const box = document.getElementById('catalogueResults');
  if(!input || !box) return;
  let t;
  function hide(){ box.style.display='none'; box.innerHTML=''; }
  function showList(list){
    if(!list || !list.length){ hide(); return; }
    box.innerHTML = list.map(p => `
      <div class="catalogue-item" data-id="${p.id}" style="padding:10px 12px;cursor:pointer;display:flex;justify-content:space-between;align-items:center;gap:8px;border-bottom:1px solid #eef2f7">
        <div style="flex:1;min-width:0">
          <div style="font-weight:700;font-size:13px;color:#0f2430;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">${p.name}</div>
          <div style="font-size:11px;color:#5b7a90">${p.category_id ? 'Category #'+p.category_id : ''} ${p.sku ? '· '+p.sku : ''} ${p.barcode ? '· '+p.barcode : ''}</div>
        </div>
        <span class="tag tag-grey" style="flex:none">Use</span>
      </div>
    `).join('');
    box.style.display='block';
    box.querySelectorAll('.catalogue-item').forEach(el=>{
      el.addEventListener('click', async ()=>{
        const id = el.dataset.id;
        // fetch full product if needed (lookup returns limited, fetch via id)
        let prod = list.find(x=> String(x.id)===String(id));
        // if not full, fetch via lookup with sku/barcode
        if(!prod.category_id && prod.sku){
          try{
            const r = await fetch('{{ route("products.lookup") }}?q='+encodeURIComponent(prod.sku)+'&sample=1', {headers:{'Accept':'application/json'}});
            const j = await r.json();
            if(j && !Array.isArray(j) && j.id) prod = j;
            else if(Array.isArray(j) && j.length) prod = j.find(x=>x.id==id) || prod;
          }catch(e){}
        }
        autofill(prod);
        hide();
        input.value = prod.name;
        if(window.toast) toast('Autofilled from: '+prod.name,'success');
      });
    });
  }
  function autofill(p){
    const setVal = (name, val)=>{
      const el = document.querySelector('[name="'+name+'"]');
      if(!el || val===null || val===undefined) return;
      if(el.tagName==='SELECT'){
        el.value = String(val);
        el.dispatchEvent(new Event('change'));
      } else {
        el.value = val;
      }
    };
    // Name
    setVal('name', p.name);
    // Generate new SKU from name
    const slug = p.name.toLowerCase().replace(/[^a-z0-9]+/g,'-').replace(/^-|-$/g,'').substring(0,12).toUpperCase();
    const sku = slug + '-' + Math.random().toString(36).substring(2,6).toUpperCase() + Math.floor(Math.random()*90+10);
    setVal('sku', sku);
    setVal('barcode', '');
    setVal('category_id', p.category_id || '');
    setVal('brand_id', p.brand_id || '');
    setVal('unit_id', p.unit_id || '');
    setVal('supplier_id', p.supplier_id || '');
    setVal('buying_price', p.buying_price ?? 0);
    setVal('selling_price', p.selling_price ?? 0);
    setVal('wholesale_price', p.wholesale_price || '');
    setVal('tax_rate', p.tax_rate ?? 0);
    setVal('description', p.description || p.name);
    if(p.product_type){
      const st = document.querySelector('[name="status"]');
      // keep status active
    }
    // Focus next field
    document.querySelector('[name="barcode"]')?.focus();
  }
  // Handle ?sample_id= autofill on load (from damp catalogue Use as Template)
  @if(isset($sample) && $sample)
    try {
      const sampleData = @json($sample);
      if(sampleData && sampleData.id){
        setTimeout(()=> {
          autofill(sampleData);
          input.value = sampleData.name;
          if(window.toast) toast('Autofilled from damp: '+sampleData.name,'success');
        }, 200);
      }
    } catch(e){}
  @endif
  input.addEventListener('input', e=>{
    const q = e.target.value.trim();
    clearTimeout(t);
    if(q.length < 2){ hide(); return; }
    t = setTimeout(async ()=>{
      try{
        const r = await fetch('{{ route("products.lookup") }}?q='+encodeURIComponent(q)+'&sample=1', {headers:{'Accept':'application/json'}});
        const data = await r.json();
        const list = Array.isArray(data) ? data : (data && data.id ? [data] : []);
        showList(list);
      }catch(e){ hide(); }
    }, 280);
  });
  document.addEventListener('click', e=>{
    if(!e.target.closest('#catalogueSearch') && !e.target.closest('#catalogueResults')) hide();
  });
});
</script>
@endsection
