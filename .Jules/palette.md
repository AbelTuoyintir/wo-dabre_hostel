## 2025-07-25 - Password Toggle Accessibility & Keyboard States
**Learning:** Icon-only buttons (like password visibility toggles) lack screen reader accessibility and key visual focus indicators for keyboard navigation. By supplying standard `aria-label` tags, updating them dynamically on toggles, and utilizing Tailwind focus rings, we ensure full accessibility compliance.
**Action:** Always add `aria-label` to custom toggle buttons and add focus indicators (`focus-visible:ring-2`) to ensure interactive visual cueing.
