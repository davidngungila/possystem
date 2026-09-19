@extends('layouts.admin')
@section('title','Product')
@section('content')
<div class="page-head">
    <div><h1>{{ $product->name }}</h1><p class="page-sub">SKU {{ $product->sku }} @if($product->barcode) · Barcode {{ $product->barcode }} @endif</p></div>
    <div style="display:flex;gap:8px;">
        <a href="{{ route('products.edit', encId($product->id)) }}" class="btn btn-ghost btn-sm">Edit</a>
        <a href="{{ route('products.index') }}" class="btn btn-ghost btn-sm">Back</a>
    </div>
</div>

<div style="display:grid;grid-template-columns:1.2fr .8fr;gap:16px">
    <div class="panel">
        <div class="panel-head"><div class="panel-title">Details</div><span class="tag {{ $product->status==='active' ? 'tag-green' : 'tag-grey' }}">{{ ucfirst($product->status) }}</span></div>
        <div class="panel-body">
            <div class="kv">
                <div class="kv-row"><span class="k">Name</span><span class="v">{{ $product->name }}</span></div>
                <div class="kv-row"><span class="k">SKU</span><span class="v mono">{{ $product->sku }}</span></div>
                <div class="kv-row"><span class="k">Barcode</span><span class="v mono">{{ $product->barcode ?? '—' }}</span></div>
                <div class="kv-row"><span class="k">Category</span><span class="v">{{ $product->category->name ?? '—' }}</span></div>
                <div class="kv-row"><span class="k">Brand</span><span class="v">{{ $product->brand->name ?? '—' }}</span></div>
                <div class="kv-row"><span class="k">Unit</span><span class="v">{{ $product->unit->name ?? '—' }}</span></div>
                <div class="kv-row"><span class="k">Supplier</span><span class="v">{{ $product->supplier->name ?? '—' }}</span></div>
                <div class="kv-row"><span class="k">Buying</span><span class="v">TZS {{ number_format($product->buying_price,0) }}</span></div>
                <div class="kv-row"><span class="k">Selling</span><span class="v" style="font-weight:800">TZS {{ number_format($product->selling_price,0) }}</span></div>
                <div class="kv-row"><span class="k">Wholesale</span><span class="v">{{ $product->wholesale_price ? 'TZS '.number_format($product->wholesale_price,0) : '—' }}</span></div>
                <div class="kv-row"><span class="k">Stock</span><span class="v">{{ $product->current_stock }} (min {{ $product->min_stock }}) @if($product->isOutOfStock()) <span class="tag tag-red">Out</span> @elseif($product->isLowStock()) <span class="tag tag-gold">Low</span> @else <span class="tag tag-green">OK</span> @endif</span></div>
                <div class="kv-row"><span class="k">Tax</span><span class="v">{{ $product->tax_rate }}%</span></div>
                <div class="kv-row"><span class="k">Expiry</span><span class="v">{{ $product->expiry_date ? $product->expiry_date->format('Y-m-d') : '—' }}</span></div>
                <div class="kv-row"><span class="k">Batch</span><span class="v">{{ $product->batch_number ?? '—' }}</span></div>
            </div>
            @if($product->description)<div style="margin-top:12px;padding:12px;background:var(--sand-50);border:1px solid var(--line);border-radius:10px;font-size:13px;color:var(--ink-soft);">{{ $product->description }}</div>@endif
        </div>
    </div>
    <div style="display:flex;flex-direction:column;gap:16px">
        <div class="panel" style="padding:16px;text-align:center;">
            <div style="font-size:11px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;color:var(--ink-soft);">Barcode Label — Real Barcode</div>
            <div style="margin-top:10px;border:1.5px dashed var(--line);border-radius:12px;padding:16px;background:var(--sand-50);display:flex;flex-direction:column;align-items:center;text-align:center;">
                <div style="font-weight:800">{{ $product->name }}</div>
                @if($product->barcode)
                    <div style="display:block;margin:8px auto;max-width:280px">{!! str_replace('<svg', '<svg id="barcodeShow"', DNS1D::getBarcodeSVG($product->barcode, 'C128', 1.8, 48, 'black', false, true)) !!}</div>
                    <div style="font-family:monospace;font-size:12px;text-align:center">{{ $product->barcode }}</div>
                @else
                    <div style="margin:8px 0;font-family:monospace;letter-spacing:.18em;color:var(--ink-soft);text-align:center">— No barcode —</div>
                    <div style="font-family:monospace;font-size:12px;text-align:center">{{ $product->sku }} (SKU)</div>
                    <div style="display:block;margin:8px auto;max-width:260px">{!! str_replace('<svg', '<svg id="barcodeShowSku"', DNS1D::getBarcodeSVG($product->sku, 'C128', 1.6, 40, 'black', false, true)) !!}</div>
                @endif
                <div style="margin-top:6px;font-weight:800;color:var(--terracotta-600);text-align:center">TZS {{ number_format($product->selling_price,0) }}</div>
            </div>
            <div style="margin-top:10px;display:flex;gap:8px;flex-wrap:wrap;justify-content:center">
                <button class="btn btn-ghost btn-sm" style="flex:1" onclick="window.print()"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg> Print Label</button>
                @if($product->barcode)
                <button class="btn btn-primary btn-sm" style="flex:1" onclick="downloadBarcodeSVG()"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/></svg> Download SVG</button>
                <button class="btn btn-primary btn-sm" style="flex:1" onclick="downloadBarcodePNG()"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M9 9h6v6H9z"/></svg> Download PNG</button>
                <button class="btn btn-primary btn-sm" style="flex:1" onclick="downloadBarcodePDF()"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><line x1="10" y1="9" x2="10" y2="9"/></svg> Download PDF</button>
                @endif
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
        <script>
        document.addEventListener('DOMContentLoaded', function(){
          @if($product->barcode)
            try{ JsBarcode("#barcodeShow", "{{ $product->barcode }}", {format:"CODE128", width:1.8, height:48, displayValue:false, margin:8}); }catch(e){}
          @else
            try{ JsBarcode("#barcodeShowSku", "{{ $product->sku }}", {format:"CODE128", width:1.6, height:40, displayValue:false, margin:6}); }catch(e){}
          @endif
        });
        function downloadBarcodeSVG(){
          const svg=document.getElementById('barcodeShow');
          if(!svg) return;
          const data=new XMLSerializer().serializeToString(svg);
          const blob=new Blob([data], {type:"image/svg+xml"});
          const url=URL.createObjectURL(blob);
          const a=document.createElement('a'); a.href=url; a.download="{{ $product->sku }}-barcode.svg"; a.click(); URL.revokeObjectURL(url);
        }
        function downloadBarcodePNG(){
          const svg=document.getElementById('barcodeShow');
          if(!svg) return;
          const canvas=document.createElement('canvas');
          const ctx=canvas.getContext('2d');
          const data=new XMLSerializer().serializeToString(svg);
          const img=new Image();
          const svgBlob=new Blob([data], {type:'image/svg+xml'});
          const url=URL.createObjectURL(svgBlob);
          img.onload=function(){
            canvas.width=svg.width.baseVal.value;
            canvas.height=svg.height.baseVal.value;
            ctx.drawImage(img,0,0);
            const png=canvas.toDataURL('image/png');
            const a=document.createElement('a'); a.href=png; a.download="{{ $product->sku }}-barcode.png"; a.click();
            URL.revokeObjectURL(url);
          };
          img.src=url;
        }
        function downloadBarcodePDF(){
          const svg=document.getElementById('barcodeShow');
          if(!svg) return;
          const { jsPDF } = window.jspdf;
          const doc=new jsPDF({orientation:'landscape', unit:'mm', format:[80, 40]});
          const canvas=document.createElement('canvas');
          const ctx=canvas.getContext('2d');
          const data=new XMLSerializer().serializeToString(svg);
          const svgBlob=new Blob([data], {type:'image/svg+xml'});
          const url=URL.createObjectURL(svgBlob);
          const img=new Image();
          img.onload=function(){
            canvas.width=svg.width.baseVal.value;
            canvas.height=svg.height.baseVal.value;
            ctx.drawImage(img,0,0);
            const imgData=canvas.toDataURL('image/png');
            const pageWidth=doc.internal.pageSize.getWidth();
            const pageHeight=doc.internal.pageSize.getHeight();
            doc.addImage(imgData, 'PNG', 0, 0, pageWidth, pageHeight);
            doc.save("{{ $product->sku }}-barcode.pdf");
            URL.revokeObjectURL(url);
          };
          img.src=url;
        }
        </script>
            <div class="panel-head"><div class="panel-title">Stock Movements</div><span class="tag tag-grey">{{ $product->stockMovements->count() }}</span></div>
            <div class="table-scroll" style="max-height:300px">
                <table>
                    <thead><tr><th>Type</th><th class="center">Qty</th><th class="center">Prev → New</th><th>Reason</th><th>At</th></tr></thead>
                    <tbody>
                    @forelse($product->stockMovements->sortByDesc('created_at')->take(20) as $m)
                        <tr><td><span class="tag {{ $m->type==='in' ? 'tag-green' : 'tag-gold' }}">{{ $m->type }}</span></td><td class="center">{{ $m->quantity }}</td><td class="center">{{ $m->previous_stock }} → {{ $m->new_stock }}</td><td>{{ $m->reason ?? '—' }}</td><td class="muted" style="font-size:12px">{{ $m->created_at->format('d/m H:i') }}</td></tr>
                    @empty
                        <tr><td colspan="5"><div class="empty-state" style="padding:16px"><strong>No movements</strong></div></td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<style>@media(max-width:900px){ div[style*="grid-template-columns:1.2fr"]{grid-template-columns:1fr !important} }</style>
@endsection
