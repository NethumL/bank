const tableRows = document.querySelectorAll('#table-body tr');
const loanID = document.getElementById('loanID');
const name = document.getElementById('name');
const accountNumber = document.getElementById('accountNumber');
const loanPlan = document.getElementById('loanPlan');
const amount = document.getElementById('amount');
const reason = document.getElementById('reason');
tableRows.forEach(e => e.addEventListener('click', function() {
    loanID.value = e.dataset.loanId;
    name.value = e.dataset.name;
    accountNumber.value = e.dataset.accountNumber;
    loanPlan.value = e.dataset.loanPlan;
    amount.value = e.dataset.amount;
    reason.value = e.dataset.reason;
}));

const approveBtn = document.getElementById('approve-btn');
approveBtn.addEventListener('click', function() {
    const loanIDVal = loanID.value;

    fetch(`http://127.0.0.1:8000/loan/approval/${loanIDVal}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json'
        },
        credentials: 'include',
        body: JSON.stringify({
            approval: true
        })
    })
        .then(res => {
            if (res.status === 200) {
                return res.json();
            } else {
                showMessage('Operation failed.', 'danger');
                return null;
            }
        })
        .then(result => {
            if (result.success) {
                location.reload();
            } else {
                showMessage('Operation failed.', 'danger');
            }
        })
        .catch(e => {
            console.log(e);
        })
});

const rejectBtn = document.getElementById('reject-btn');
rejectBtn.addEventListener('click', function() {
    const loanIDVal = loanID.value;

    fetch(`http://127.0.0.1:8000/loan/approval/${loanIDVal}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json'
        },
        redirect: 'follow',
        body: JSON.stringify({
            approval: false
        })
    })
        .then(res => {
            if (res.status === 200) {
                return res.json();
            } else {
                showMessage('Operation failed.', 'danger');
                return null;
            }
        })
        .then(result => {
            if (result.success) {
                location.reload();
            } else {
                showMessage('Operation failed.', 'danger');
            }
        })
        .catch(e => {
            console.log(e);
        })
});

const showMessage = (message, type) => {
    const messageBox = document.getElementById('message-box');
    messageBox.classList.add(`alert-${type}`);
    const string = document.createTextNode(message);
    messageBox.appendChild(string);
    messageBox.style.display = 'block';
}