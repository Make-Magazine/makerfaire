import { useState, useEffect } from "react";
import VisualRadioCards from "./VisualRadioCards.jsx";

const wpI18n = (window.wp && window.wp.i18n) || {};
const _n = wpI18n._n || ((single, plural, count) => (count === 1 ? single : plural));
const sprintf = wpI18n.sprintf || ((format, ...args) => format.replace(/%d/, args[0]));

/**
 * Per-theme card preview palettes. Legacy shows a neutral admin-gray sample;
 * Vantage shows its electric-blue-on-navy token palette so the two read as
 * visibly different looks at a glance.
 */
const THEME_PREVIEWS = {
  legacy: { surface: "#ffffff", text: "#23282d", swatches: ["#f6f7f7", "#bbbbbb", "#50575e", "#cccccc"] },
  vantage: { surface: "#ffffff", text: "#112337", swatches: ["#204ce5", "#112337", "#54667a", "#e5e7eb"] },
};

/** Strips the browser's fieldset/legend chrome so the groups keep the exact look of the former div + span markup. */
const FIELDSET_RESET = { border: 0, margin: 0, padding: 0, minWidth: 0 };
const LEGEND_RESET = { padding: 0 };

/**
 * Renders the theme picker (always) and the column picker (only when a
 * non-legacy theme is active, mirroring `requires_not theme=legacy`). Both
 * pickers control the existing PHP-rendered inputs and dispatch native change
 * events, so the form save and the admin-views.js dependent-field gating keep
 * working unchanged.
 */
export default function ThemePicker({ themeNode, columnsNode, equalHeightNode, equalHeightRow, templateNode, gridTemplates = [], i18n }) {
  const __ = i18n || ((s) => s);

  const themeOptions = Array.from(themeNode.options).map((o) => ({
    value: o.value,
    label: o.textContent.trim(),
    ...(THEME_PREVIEWS[o.value] || {}),
  }));
  const [theme, setTheme] = useState(themeNode.value || "legacy");

  const colMin = Number(columnsNode && columnsNode.min) || 1;
  const colMax = Number(columnsNode && columnsNode.max) || 6;
  const colOptions = [];
  for (let i = colMin; i <= colMax; i++) {
    colOptions.push({
      value: String(i),
      label: String(i),
      ariaLabel: sprintf(_n("%d column", "%d columns", i, "gk-gravityview"), i),
    });
  }
  const [columns, setColumns] = useState(String((columnsNode && columnsNode.value) || "1"));

  // Mirror the native Equal Height checkbox so the column preview bars show
  // matched heights when it is on and staggered (natural) heights when off.
  // The checkbox lives outside this widget (PHP-rendered), so subscribe to it.
  const [equalHeight, setEqualHeight] = useState(equalHeightNode ? !!equalHeightNode.checked : true);
  useEffect(() => {
    if (!equalHeightNode) {
      return undefined;
    }
    const onToggle = () => setEqualHeight(!!equalHeightNode.checked);
    equalHeightNode.addEventListener("change", onToggle);
    return () => equalHeightNode.removeEventListener("change", onToggle);
  }, [equalHeightNode]);

  // Columns + Equal Height only apply to grid-aware layouts (List, Layout
  // Builder, DIY, Map), not table-based ones (Table, DataTables). Track the
  // View's layout so the pickers follow a live layout switch.
  const [template, setTemplate] = useState(templateNode ? templateNode.value : "");
  useEffect(() => {
    if (!templateNode) {
      return undefined;
    }
    const onChange = () => setTemplate(templateNode.value);
    // The Layout Switcher updates the template with jQuery's `.trigger('change')`
    // (admin-views.js), which a native addEventListener never observes, so bind
    // through jQuery. It is always loaded on the View edit screen and also
    // catches native changes; a native fallback could not see the layout switch
    // anyway, so skip binding if jQuery is somehow absent.
    const jq = window.jQuery;
    if (!jq) {
      return undefined;
    }
    const $node = jq(templateNode);
    $node.on("change.gvThemePicker", onChange);
    return () => $node.off("change.gvThemePicker", onChange);
  }, [templateNode]);
  const gridAware = gridTemplates.length === 0 || gridTemplates.includes(template);

  // The Equal Height row is a native PHP-rendered row gated by `requires`
  // (theme + columns). Layer the layout gate on top with a !important class so
  // it beats the inline display the `requires` toggling writes.
  useEffect(() => {
    if (!equalHeightRow) {
      return;
    }
    equalHeightRow.classList.toggle("gv-theme-picker-layout-hide", !gridAware);
  }, [gridAware, equalHeightRow]);

  function sync(node, value) {
    if (!node) {
      return;
    }
    node.value = value;
    node.dispatchEvent(new Event("change", { bubbles: true }));
    node.dispatchEvent(new Event("input", { bubbles: true }));
  }

  const showColumns = columnsNode && theme !== "legacy" && gridAware;

  return (
    <div className="gv-theme-picker">
      <fieldset className="gv-theme-picker__field" style={FIELDSET_RESET}>
        <legend className="gv-theme-picker__label" style={LEGEND_RESET}>{__("Theme", "gk-gravityview")}</legend>
        <VisualRadioCards
          options={themeOptions}
          value={theme}
          previewType="theme"
          name="gv-theme-picker-theme"
          onChange={(v) => {
            setTheme(v);
            sync(themeNode, v);
          }}
        />
      </fieldset>

      {showColumns && (
        <fieldset className="gv-theme-picker__field" style={{ ...FIELDSET_RESET, marginTop: 16 }}>
          <legend className="gv-theme-picker__label" style={LEGEND_RESET}>{__("Columns", "gk-gravityview")}</legend>
          <VisualRadioCards
            options={colOptions.map((o) => ({ ...o, equalHeight }))}
            value={columns}
            previewType="columns"
            name="gv-theme-picker-columns"
            onChange={(v) => {
              setColumns(v);
              sync(columnsNode, v);
            }}
          />
        </fieldset>
      )}
    </div>
  );
}
