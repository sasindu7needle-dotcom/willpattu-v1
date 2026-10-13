<div class="editor-field-grid">
<label>Room name<input name="name" maxlength="120" value="{{ $useOld ? old('name', $room->name ?? '') : $room->name }}" required></label>
<label>Total rooms<input name="inventory" type="number" min="0" max="100" value="{{ $useOld ? old('inventory', $room->inventory ?? 0) : $room->inventory }}" required></label>
<label>Maximum guests per room (including children)<input name="capacity" type="number" min="1" max="20" value="{{ $useOld ? old('capacity', $room->capacity ?? 2) : $room->capacity }}" required></label>
<label>Sri Lankan nightly rate (LKR)<input name="local_rate" type="number" min="1" max="1000000" step=".01" value="{{ $useOld ? old('local_rate', $room->local_rate ?? '') : $room->local_rate }}" required></label>
<label>International nightly rate (LKR)<input name="international_rate" type="number" min="1" max="1000000" step=".01" value="{{ $useOld ? old('international_rate', $room->international_rate ?? '') : $room->international_rate }}" required></label>
<label>Visibility<select name="is_active"><option value="0" @selected(!($useOld ? old('is_active', $room->is_active ?? false) : $room->is_active))>Hidden — unavailable online</option><option value="1" @selected($useOld ? old('is_active', $room->is_active ?? false) : $room->is_active)>Published — accept bookings</option></select></label>
</div><label>Description<textarea name="description" rows="3" maxlength="2000" required>{{ $useOld ? old('description', $room->description ?? '') : $room->description }}</textarea></label>
