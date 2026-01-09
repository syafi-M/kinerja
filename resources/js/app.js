import "./bootstrap";

import Alpine from "alpinejs";
import { collapse } from "@alpinejs/collapse";
import "remixicon/fonts/remixicon.css";

window.Alpine = Alpine;

Alpine.plugin(collapse);
Alpine.start();
