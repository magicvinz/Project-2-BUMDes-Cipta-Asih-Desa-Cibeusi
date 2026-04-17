import './bootstrap';
import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

import * as Turbo from "@hotwired/turbo";
import NProgress from "nprogress";

// Mengaktifkan Hotwire Turbo
Turbo.start();

// Konfigurasi NProgress untuk Turbo
NProgress.configure({ showSpinner: false });
document.addEventListener("turbo:visit", () => {
    NProgress.start();
});

document.addEventListener("turbo:submit-start", () => {
    NProgress.start();
});

document.addEventListener("turbo:load", () => {
    NProgress.done();
});
