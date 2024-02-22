document.addEventListener('DOMContentLoaded', () => {
    if (!payeyeAdmin) {
        return;
    }
    payeyeAdmin.toggles.forEach((data)=>{
        let toggle = document.getElementById(data.id);
        if (!toggle) {
            return;
        }
        hideUIFields(data, toggle.value);
        toggle.addEventListener('change', ()=>{
            hideUIFields(data, toggle.value);
        })
    })
});

function hideUIFields(data, value) {
    let ids = data.values[value];
    if (!ids) {
        return;
    }
    setFieldsDisplay(ids.hide, 'none');
    setFieldsDisplay(ids.show, 'block');
}
function setFieldsDisplay(values, style) {
    const allowedDisplay = ['none', 'block'];
    if (!style in allowedDisplay) {
        return;
    }
    Object.keys(values).forEach((key)=>{
        let id = values[key];
        let input = document.querySelector(`[name="${id}"]`);
        if (!input) {
            return;
        }
        let i = 10;
        let parent = input.parentElement;
        for (i; i > 0; i--) {
            if (parent.classList.contains('form-group')) {
                console.log(parent)
                parent.style.display = style;
                break;
            }
            parent = parent.parentElement;
        }
    })
}