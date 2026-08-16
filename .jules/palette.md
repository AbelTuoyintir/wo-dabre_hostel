# Palette's UX Journal

Critical UX/accessibility learnings and reusable patterns for the UCC Hostel Booking System.

## 2026-03-06 - [Avoiding Floating Widget Overlaps and Placement Conflicts]
**Learning:** Floating action buttons and widgets (such as quick action menus and support bubbles) are highly prone to physical overlap and z-index context conflicts when placed independently on individual views using the same spatial positioning (e.g., bottom-right). Extracting page-level floating controls into a single global layout widget placed on the opposite side of the viewport (e.g., bottom-left) ensures visual clarity, prevents interaction blocking with support widgets, and unifies the design system.
**Action:** Consolidate isolated floating triggers into a cohesive, keyboard-friendly global quick actions menu using Alpine.js and position it to avoid colliding with other persistent bottom widgets.

## 2026-03-05 - [Keyboard-Accessible Star Rating Controls]
**Learning:** Interactive star rating controls are often built using decorative icons (e.g., `<i>` tag with hover events) which are completely inaccessible to keyboard-only and screen-reader users. Replacing icons with semantic `<button type="button">` elements equipped with explicit dynamic `aria-label` tags, focused-visible rings, and separating hovering preview state from fixed selected rating state solves this.
**Action:** Always wrap interactive rating stars in semantic buttons and manage both `rating` and `hoverRating` states dynamically in Alpine.js.

## 2026-03-05 - [Self-Correction on Interactive Link Nesting]
**Learning:** Nesting interactive elements like `<button>` or `<input>` inside an outer anchor link `<a>` breaks screen-readers and keyboard navigation, causing unexpected focus order.
**Action:** Lift interactive controls (e.g., favorite/wishlist buttons, selection/comparison checkboxes) out of the anchor element by wrapping the card in a parent relative `div`.

## 2026-03-05 - [Aria-Labeling and Accessible Focus Indicators]
**Learning:** Icon-only buttons and checkboxes must always contain descriptive `aria-label` tags and visible focus rings (`focus-within:ring-2` / `focus-visible:ring-2`) to keep interfaces accessible to keyboard-only and screen-reader users.
**Action:** Replace `hidden` inputs with `sr-only` so they remain focusable, and style focused states on their visual wrappers.

## 2026-03-05 - [Image Carousel and Floating Control Accessibility]
**Learning:** Interactive sliding carousels, dynamic toggle quick-actions, and comparison action triggers containing icon-only elements should have precise descriptive `aria-label` labels and focus visible offset outlines to support robust keyboard focus tracking.
**Action:** Implement `aria-label`, `aria-expanded` and `focus-visible:ring-yellow-400` styling directly on the image slider navigation buttons and action items.

## 2026-03-05 - [Dynamic Aria-Label and Chat Input Focus Retention]
**Learning:** Heart icon buttons and dynamic action controls should synchronize their `aria-label` and `title` attributes on click to prevent screen-readers and visual hover users from receiving stale labels (like "Add..." when the item is already added). In messaging views, always programmatically refocus the text input after form submission so keyboard focus is not lost and users can type consecutively.
**Action:** Always update the attributes (`aria-label`, `title`) dynamically in event handlers, and use Alpine.js `$nextTick` with `$refs` to restore input focus on submit.
