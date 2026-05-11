import "./bootstrap";

import Alpine from "alpinejs";
import { collapse } from "@alpinejs/collapse";
import "remixicon/fonts/remixicon.css";
import TomSelect from "tom-select";
import "tom-select/dist/css/tom-select.css";

window.Alpine = Alpine;

Alpine.plugin(collapse);
Alpine.start();

window.initTomUserSelect = function initTomUserSelect(selectId, options = {}) {
  const selectElement = document.getElementById(selectId);
  if (!selectElement) return null;
  if (selectElement.tomselect) return selectElement.tomselect;

  const instance = new TomSelect(selectElement, {
    create: false,
    dropdownParent: "body",
    maxOptions: 1000,
    searchField: ["text"],
    sortField: [{ field: "text", direction: "asc" }],
    placeholder: "Pilih user",
    ...options,
  });

  selectElement.style.display = "none";

  const wrapperClassesToRemove = [
    "min-h-11",
    "w-full",
    "rounded-lg",
    "border",
    "border-slate-300",
    "bg-white",
    "px-3",
    "py-2.5",
    "text-sm",
    "text-slate-800",
    "outline-none",
    "transition",
    "focus:border-sky-500",
    "focus:ring-2",
    "focus:ring-sky-100",
    "border-red-400",
    "focus:border-red-500",
    "focus:ring-red-100",
  ];

  instance.wrapper.classList.remove(...wrapperClassesToRemove);
  instance.wrapper.classList.add("tom-user-select");

  return instance;
};
