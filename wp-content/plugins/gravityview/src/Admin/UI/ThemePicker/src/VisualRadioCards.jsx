import { useId } from "react";

/**
 * Visual radio-card group, ported from the 3.0 Design Studio's
 * VisualRadioCards. The customer picks by looking at a sample rather than
 * decoding a label. Native radios power the semantics (keyboard nav + group
 * name); the cards are visual chrome on top. The caller labels the group by
 * wrapping this component in a fieldset with a legend.
 *
 *   previewType:
 *     "columns" -> N rectangles arranged in N columns
 *     "theme"   -> a mini card with a typography sample + color swatches
 *     (fallback) -> the label
 *
 * An option's `ariaLabel` replaces its visible label as the radio's
 * accessible name.
 */
export default function VisualRadioCards({ options = [], value, onChange, previewType = "label", name }) {
  const autoName = useId();
  const groupName = name || `gv-vrc-${autoName}`;

  return (
    <div className="gv-vrc" style={{ display: "flex", gap: 8, flexWrap: "wrap", margin: 0 }}>
      {options.map((opt) => {
        const isActive = String(value) === String(opt.value);
        return (
          <label
            key={String(opt.value)}
            className={`gv-vrc__card${isActive ? " is-active" : ""}`}
            style={{
              display: "inline-flex",
              flexDirection: "column",
              alignItems: "center",
              gap: 6,
              padding: "10px 12px",
              border: `1px solid ${isActive ? "#2271b1" : "#dcdcde"}`,
              borderRadius: 6,
              cursor: "pointer",
              background: isActive ? "#f0f6fc" : "#fff",
              minWidth: 64,
              boxShadow: isActive ? "0 0 0 1px #2271b1 inset" : undefined,
              transition: "border-color 120ms ease, background 120ms ease",
            }}
          >
            <input
              type="radio"
              name={groupName}
              value={String(opt.value)}
              aria-label={opt.ariaLabel}
              checked={isActive}
              onChange={() => onChange && onChange(opt.value)}
              style={{ position: "absolute", width: 1, height: 1, padding: 0, margin: -1, overflow: "hidden", clip: "rect(0,0,0,0)", whiteSpace: "nowrap", border: 0 }}
            />
            <Preview previewType={previewType} option={opt} />
            <span style={{ fontSize: 11, color: "#1d2327", textAlign: "center", lineHeight: 1.2 }}>{opt.label}</span>
          </label>
        );
      })}
    </div>
  );
}

function Preview({ previewType, option }) {
  if (previewType === "columns") {
    const cols = Number(option.value) || 1;
    // `option.equalHeight` is carried on the column option (not a generic prop)
    // since it only affects this preview: on -> bars fill the row (matched
    // heights); off -> bars are top-aligned with varying heights so their
    // bottoms stagger to read as "natural heights." Heights are a fixed
    // organic pattern, not random, so the preview is stable across renders.
    const equalHeight = option.equalHeight !== false;
    const STAGGER = [28, 19, 25, 16, 23, 20];
    return (
      <span
        aria-hidden="true"
        style={{ display: "grid", gridTemplateColumns: `repeat(${cols}, 1fr)`, gap: 2, width: 56, height: 28, alignItems: equalHeight ? "stretch" : "start" }}
      >
        {Array.from({ length: cols }).map((_, i) => (
          <span key={i} style={{ background: "#2271b1", borderRadius: 2, height: equalHeight ? "100%" : STAGGER[i % STAGGER.length] }} />
        ))}
      </span>
    );
  }

  if (previewType === "theme") {
    const swatches = option.swatches || [];
    return (
      <span aria-hidden="true" style={{ display: "flex", flexDirection: "column", gap: 4, width: 72 }}>
        <span
          style={{
            height: 30,
            borderRadius: 4,
            background: option.surface || "#ffffff",
            border: "1px solid #dcdcde",
            display: "flex",
            alignItems: "center",
            justifyContent: "center",
            color: option.text || "#1d2327",
            fontSize: 13,
            fontWeight: 600,
          }}
        >
          Aa
        </span>
        <span style={{ display: "flex", gap: 2 }}>
          {swatches.map((c, i) => (
            <span key={i} style={{ flex: 1, height: 6, borderRadius: 2, background: c }} />
          ))}
        </span>
      </span>
    );
  }

  return (
    <span aria-hidden="true" style={{ fontSize: 11, color: "#1d2327" }}>
      {option.label}
    </span>
  );
}
