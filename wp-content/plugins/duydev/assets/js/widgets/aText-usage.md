# AText Animation - Usage Guide

## Basic Usage

Simply add the `atext` class to any element containing text:

```html
<h1 class="atext">This text will animate</h1>
<p class="atext">This paragraph will also animate</p>
```

## Customization with Data Attributes

You can customize the animation using data attributes:

```html
<h1
  class="atext"
  data-stagger="0.05"
  data-duration="1.5"
  data-ease="cubic-bezier(0.68, -0.55, 0.265, 1.55)"
>
  Custom animated text
</h1>
```

### Available Data Attributes:

- `data-stagger`: Delay between each character (default: 0.02)
- `data-duration`: Animation duration in seconds (default: 1)
- `data-ease`: CSS transition easing (default: cubic-bezier(0.19, 1, 0.22, 1))
- `data-auto-trigger`: Enable/disable auto trigger on scroll (default: true)

## Manual Control

### Using CSS Classes:

```javascript
// Trigger animation
element.classList.add("atext-trigger");

// Reset animation
element.classList.add("atext-reset");
```

### Using JavaScript API:

```javascript
// Animate in
ATextManager.animateIn(element);

// Animate out
ATextManager.animateOut(element);

// Replay animation
ATextManager.replay(element);

// Create new instance
ATextManager.create(element);
```

## Events

Listen for animation events:

```javascript
element.addEventListener("atext:animateIn", (e) => {
  console.log("Animation started", e.detail.instance);
});

element.addEventListener("atext:animateOut", (e) => {
  console.log("Animation ended", e.detail.instance);
});
```

## CSS Helper Classes

Use predefined animation styles:

```html
<p class="atext atext-fast">Fast animation</p>
<p class="atext atext-slow">Slow animation</p>
<p class="atext atext-bounce">Bouncy animation</p>
```

## Integration with Yeori Slide Widget

For your Yeori Slide widget, you can now simply add the `atext` class:

```php
<section class="panel p<?php echo ($i+1); ?>" style="background:<?php echo esc_attr($slide['background']); ?>">
    <h1 class="atext"><?php echo esc_html($slide['heading']); ?></h1>
    <p class="atext"><?php echo esc_html($slide['content']); ?></p>
</section>
```

And trigger animations based on active panel:

```javascript
// In your slide logic
function updateActivePanel(panelIndex) {
  // Reset all panels
  document.querySelectorAll(".panel .atext").forEach((el) => {
    ATextManager.animateOut(el);
  });

  // Animate current panel
  const currentPanel = document.querySelector(
    `.panel:nth-child(${panelIndex + 1})`
  );
  currentPanel.querySelectorAll(".atext").forEach((el) => {
    ATextManager.animateIn(el);
  });
}
```

## Requirements

- GSAP with SplitText plugin
- Modern browser with Intersection Observer support

## Browser Support

- Chrome 58+
- Firefox 55+
- Safari 12.1+
- Edge 79+
