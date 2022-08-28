import './styles/app.scss';
import {toggleMenu} from "./components/responsive-menu";
import TomSelect from "tom-select";
import 'tom-select/src/scss/tom-select.scss'
import * as Turbo from "@hotwired/turbo"
import {registerServiceWorker} from "./ServiceWorkerRegister";

document.addEventListener("turbo:load", function () {
    document.dispatchEvent(new CustomEvent("onLoad"))
})


function loadTomSelect() {
    document.querySelectorAll("select.ts-select").forEach(x => {
        let plugins = ['remove_button', 'dropdown_input']
        if (x.multiple) {
            plugins.push("checkbox_options")
        }

        new TomSelect(x, {
            plugins: plugins,
        });

    })
}

window.loadTomSelect = loadTomSelect;

loadTomSelect()
document.addEventListener("turbo:render", loadTomSelect)

document.addEventListener("turbo:submit-start", ({target}) => {
    for (const field of target.elements) {
        field.disabled = true
    }
})

window.toggleMenu = toggleMenu;
window.Turbo = Turbo;


if ('serviceWorker' in navigator) {
    registerServiceWorker().then(() => {
    });
}
