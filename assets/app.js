import './styles/app.scss';
import {toggleMenu} from "./components/responsive-menu";
import TomSelect from "tom-select";
import 'tom-select/src/scss/tom-select.scss'
window.toggleMenu = toggleMenu;

document.querySelectorAll("select.ts-select").forEach(x => {
    let plugins = ['remove_button', 'dropdown_input']
    if (x.multiple)
        plugins.push("checkbox_options")
    new TomSelect(x, {
        plugins: plugins,
    });
})