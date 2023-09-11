export default class extends window.Controller {
    checked(event) {
        if (event.target.nodeName !== 'INPUT') {
            return;
        }

        const wrapper = event.target.closest('div');

        wrapper.querySelectorAll('input').forEach((input) => {
            input.removeAttribute('checked');
        });
        wrapper.querySelectorAll('label').forEach((label) => {
            label.classList.remove('active');
        });
        event.target.closest('label').classList.add('active');
        event.target.setAttribute('checked', 'checked');
        event.target.dispatchEvent(new Event('change'));
    }
}
