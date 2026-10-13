const selection = document.querySelector('[data-room-selection]');
if (selection) {
    const quantities = [...selection.querySelectorAll('[data-room-quantity]')];
    const update = () => {
        const count = quantities.reduce((sum, input) => sum + Number(input.value || 0), 0);
        const capacity = quantities.reduce((sum, input) => sum + Number(input.value || 0) * Number(input.dataset.capacity), 0);
        const total = quantities.reduce((sum, input) => sum + Number(input.value || 0) * Number(input.dataset.rate) * Number(selection.dataset.nights), 0);
        selection.querySelector('[data-selection-count]').textContent = count ? count + ' rooms selected · Space for ' + capacity + ' guests' : 'Choose your rooms to see the total.';
        selection.querySelector('[data-selection-total]').textContent = 'LKR ' + (total / 100).toLocaleString('en-LK', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        selection.querySelector('[data-capacity-warning]').textContent = count && capacity < Number(selection.dataset.guests) ? 'Please add enough rooms for all your guests.' : '';
        selection.querySelector('button[type="submit"]').disabled = !count || capacity < Number(selection.dataset.guests);
    };
    quantities.forEach(input => input.addEventListener('change', update));
    update();
}
document.querySelectorAll('[data-confirm-booking]').forEach(form => form.addEventListener('submit', () => {
    const button = form.querySelector('button[type="submit"]');
    button.disabled = true;
    button.textContent = 'Confirming your booking…';
}));
