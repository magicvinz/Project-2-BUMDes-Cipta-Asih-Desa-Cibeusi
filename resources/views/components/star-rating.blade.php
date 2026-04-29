{{--
    Komponen: Interactive Star Rating (gaya Shopee/Gojek)
    Props:
        $name   — nama field input hidden (default: 'rating')
        $value  — nilai awal bintang 1-5 (default: 5)
        $id     — id unik untuk komponen ini (default: 'star-rating')
    
    Cara pakai:
        @include('components.star-rating', ['name' => 'rating', 'value' => 5, 'id' => 'rating-form1'])
--}}
@php
    $srName  = $name  ?? 'rating';
    $srValue = (int) ($value ?? 5);
    $srId    = $id    ?? 'star-rating-' . uniqid();
    $labels  = ['', 'Kecewa', 'Kurang', 'Cukup', 'Puas', 'Sangat Puas'];
@endphp

<style>
.star-rating-widget { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
.star-rating-widget .stars { display: flex; flex-direction: row-reverse; gap: 4px; }
.star-rating-widget .stars input[type="radio"] { display: none; }
.star-rating-widget .stars label {
    font-size: 2rem;
    color: #d1d5db;
    cursor: pointer;
    transition: color 0.15s;
    line-height: 1;
    user-select: none;
}
/* Highlight bintang yang dipilih dan semua bintang sesudahnya (karena flex-direction: row-reverse) */
.star-rating-widget .stars input:checked ~ label {
    color: #f59e0b;
}
.star-rating-widget .star-label-text {
    font-size: 0.9rem;
    font-weight: 600;
    color: #f59e0b;
    min-width: 90px;
    transition: opacity 0.15s;
}
</style>

<div class="star-rating-widget" id="{{ $srId }}-wrap">
    {{-- Hidden input yang dikirim ke server --}}
    <input type="hidden" name="{{ $srName }}" id="{{ $srId }}-input" value="{{ $srValue }}" required>

    {{-- Bintang (render dari 5 ke 1 karena flex-direction: row-reverse) --}}
    <div class="stars" id="{{ $srId }}-stars">
        @for($i = 5; $i >= 1; $i--)
        <label for="{{ $srId }}-star-{{ $i }}"
               title="{{ $labels[$i] }}"
               data-val="{{ $i }}">&#9733;</label>
        <input type="radio"
               id="{{ $srId }}-star-{{ $i }}"
               name="{{ $srId }}-radio"
               value="{{ $i }}"
               {{ $srValue == $i ? 'checked' : '' }}>
        @endfor
    </div>

    {{-- Label teks di samping --}}
    <span class="star-label-text" id="{{ $srId }}-label">{{ $labels[$srValue] }}</span>
</div>

<script>
(function() {
    var labels   = ['', 'Kecewa', 'Kurang', 'Cukup', 'Puas', 'Sangat Puas'];
    var wrap     = document.getElementById('{{ $srId }}-wrap');
    if (!wrap) return;

    var hiddenInput = document.getElementById('{{ $srId }}-input');
    var labelEl     = document.getElementById('{{ $srId }}-label');
    var starsEl     = document.getElementById('{{ $srId }}-stars');
    var labelEls    = starsEl.querySelectorAll('label');

    // Sync visual dari nilai tersimpan
    function syncVisual(val) {
        labelEls.forEach(function(l) {
            var v = parseInt(l.getAttribute('data-val'));
            l.style.color = v <= val ? '#f59e0b' : '#d1d5db';
        });
        if (labelEl) {
            labelEl.textContent = labels[val] || '';
        }
    }

    // Init
    syncVisual(parseInt(hiddenInput.value) || 5);

    // Klik bintang
    starsEl.addEventListener('click', function(e) {
        var lbl = e.target.closest('label');
        if (!lbl) return;
        var val = parseInt(lbl.getAttribute('data-val'));
        if (!val) return;
        hiddenInput.value = val;
        syncVisual(val);
        // Set radio juga agar accessible
        var radio = document.getElementById('{{ $srId }}-star-' + val);
        if (radio) radio.checked = true;
    });


})();
</script>
