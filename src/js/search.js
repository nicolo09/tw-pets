const form = document.getElementById("search-form")

document.addEventListener('keypress', function(e) {
    if(e.key === 'Enter'){
        form.submit();
    }
})

document.getElementById("search-button").addEventListener('click', () => {
    form.submit();
})