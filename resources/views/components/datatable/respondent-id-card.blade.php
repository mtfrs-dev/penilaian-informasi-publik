<div class="relative block w-96 aspect-[4300/2699] lg:flex lg:items-center lg:justify-center rounded-sm overflow-hidden bg-white border-2 border-primary-30 focus:ring-primary-70 focus:border-primary-70 p-1 group">
    <img id="image_preview" src="{{ $row->profile_picture ? asset('storage/'.$row->profile_picture) : asset('design/BLANK-ID-CARD.png') }}" class="object-contain rounded-sm" alt="FOTO KARTU PEGAWAI">
</div>