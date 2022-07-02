const form = document.querySelector("form[data-savings-id][data-amount-id]");

const savingsSelectId = form.dataset.savingsId ?? "fixed_deposit_savingsAccount";
const amountInputId = form.dataset.amountId ?? "fixed_deposit_amount";

const savingsSelect = document.querySelector(`select#${savingsSelectId}`);
const amountLabel = document.querySelector(`label[for='${amountInputId}']`)

function updateAmountLabel() {
    const selectedOption = savingsSelect.selectedOptions[0];
    const amount = selectedOption.dataset.amount;
    amountLabel.textContent = "Amount (<" + amount + ")";
}

savingsSelect.addEventListener('change', updateAmountLabel);

updateAmountLabel()
