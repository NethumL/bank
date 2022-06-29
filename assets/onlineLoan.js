let fdList = document.getElementById('online_loan_fdId');
let maxAmount = document.getElementById('max-amount');

maxAmount.innerText = fdList.options[fdList.selectedIndex].dataset.amount;

fdList.addEventListener('change', () => {
    maxAmount.innerText = fdList.options[fdList.selectedIndex].dataset.amount;
})
