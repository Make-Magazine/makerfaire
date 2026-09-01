import { createRoot } from "react-dom/client";
import ThemePicker from "./ThemePicker.jsx";

/**
 * Entry point. Progressive enhancement: the Styles tab renders the real
 * `theme` <select> and `grid_columns` <input> via PHP (so no-JS still saves
 * and the dependent-field gating works). This widget mounts at
 * `#gv-theme-picker-mount`, takes over those controls visually, and writes
 * back to them on change. If the mount point or the theme control is absent,
 * it no-ops and the native controls remain.
 */
const __ = (window.wp && window.wp.i18n && window.wp.i18n.__) || ((s) => s);

function mount() {
  const target = document.getElementById("gv-theme-picker-mount");
  if (!target || target.dataset.mounted) {
    return;
  }

  const themeNode = document.querySelector('[name="template_settings[theme]"]');
  if (!themeNode) {
    return;
  }

  const columnsNode = document.querySelector('[name="template_settings[grid_columns]"]');
  const equalHeightNode = document.querySelector('input[type="checkbox"][name="template_settings[card_equal_height]"]');
  const equalHeightRow = equalHeightNode ? equalHeightNode.closest("tr") : null;

  // The View's layout (Table, List, DataTables, ...) and the grid-aware list.
  // Columns + Equal Height only apply to grid-aware layouts; the list already
  // lives on the native rows as `data-show-if`, so reuse it rather than
  // duplicating it here.
  const templateNode = document.getElementById("gravityview_directory_template");
  const gridSource = (columnsNode && columnsNode.closest("[data-show-if]")) || equalHeightRow;
  const gridTemplates = gridSource ? (gridSource.getAttribute("data-show-if") || "").split(/\s+/).filter(Boolean) : [];

  createRoot(target).render(
    <ThemePicker
      themeNode={themeNode}
      columnsNode={columnsNode}
      equalHeightNode={equalHeightNode}
      equalHeightRow={equalHeightRow}
      templateNode={templateNode}
      gridTemplates={gridTemplates}
      i18n={__}
    />
  );

  // Only after a successful mount: flag the target and hide the native rows the
  // widget now drives. If createRoot() throws (e.g. WordPress < 6.2, where
  // ReactDOM.createRoot is unavailable), the original controls stay usable.
  target.dataset.mounted = "1";
  document.querySelectorAll(".gv-theme-picker-hide").forEach((el) => {
    el.style.display = "none";
  });
}

if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", mount, { once: true });
} else {
  mount();
}
