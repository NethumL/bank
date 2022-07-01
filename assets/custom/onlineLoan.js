const form = document.querySelector('form[data-fd-select-id]');

const fdSelectId = form.dataset.fdSelectId ?? 'online_loan_fdId';
const amountLabel = document.getElementById('max-amount');

const fdSelect = document.querySelector(`select#${fdSelectId}`);

amountLabel.innerText = fdSelect.options[fdSelect.selectedIndex].dataset.amount;

fdSelect.addEventListener('change', () => {
    amountLabel.innerText = fdSelect.options[fdSelect.selectedIndex].dataset.amount;
})
