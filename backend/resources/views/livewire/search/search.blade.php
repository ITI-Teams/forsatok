<div class="position-relative">
    <i class="fa-solid fa-magnifying-glass position-absolute text-secondary"
       style="left: 0.9rem; top: 50%; transform: translateY(-50%); z-index: 1;"></i>
    <input type="text" 
           wire:model.live="query" 
           placeholder="{{ $placeholder }}"
           class="form-control ps-5"
           style="min-width: 250px;" />
</div>
