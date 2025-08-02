# Fancybox Implementation - Mascot Index

## Fitur yang Diimplementasikan

### 🖼️ **Image Modal dengan Fancybox**

- **Library**: Fancybox v5.0
- **CDN**: https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/

### ✨ **Fitur Lengkap**

#### 1. **Basic Image Viewing**

- Click pada gambar project atau submission notes untuk membuka modal
- Smooth animations dengan fade in/out effects
- Responsive design untuk semua device

#### 2. **Advanced Controls**

- **Zoom**: Zoom in/out dengan mouse wheel atau tombol
- **Pan**: Drag gambar untuk melihat area yang di-zoom
- **Rotate**: Putar gambar searah dan berlawanan jarum jam
- **Flip**: Mirror gambar horizontal dan vertikal
- **Fullscreen**: Mode fullscreen untuk viewing optimal

#### 3. **Gallery Features**

- **Individual Gallery**: Setiap project memiliki gallery sendiri (project + submission notes)
- **Navigation**: Arrow keys atau tombol next/previous
- **Thumbnails**: Toggle thumbnail view untuk navigasi cepat
- **Slideshow**: Auto-play slideshow dengan kontrol kecepatan

#### 4. **View All Options**

- **View All Project Images**: Tombol untuk melihat semua gambar project dalam satu gallery
- **View All Submission Notes**: Tombol untuk melihat semua submission notes dalam satu gallery

#### 5. **UI/UX Enhancements**

- **Purple Theme**: Custom styling sesuai tema aplikasi (#8b5cf6)
- **Dark Mode Support**: Styling khusus untuk dark mode
- **Indonesian Localization**: Semua teks dalam bahasa Indonesia
- **Hover Effects**: Smooth hover animations
- **Responsive Toolbar**: Toolbar yang responsive dengan kontrol lengkap

#### 6. **Technical Features**

- **Preloading**: Preload 3 gambar untuk performance optimal
- **Infinite Carousel**: Loop infinite untuk navigation
- **Drag to Close**: Drag down untuk menutup modal
- **Keyboard Support**: Kontrol dengan keyboard (ESC, Arrow keys, dll)
- **Touch Support**: Gesture support untuk mobile devices

### 🎨 **Custom Styling**

#### CSS Features:

- Custom backdrop dengan blur effect
- Purple accent colors (#8b5cf6 untuk light mode, #a78bfa untuk dark mode)
- Rounded corners dan shadow effects
- Smooth transitions dan animations
- Responsive button styling

#### Gallery View Buttons:

- Rounded button design dengan hover effects
- Purple primary color dengan shadow effects
- Responsive spacing dan typography

### 🔧 **Configuration Options**

```javascript
Fancybox.bind('[data-fancybox]', {
  // Zoom settings
  Images: {
    zoom: true,
    Panzoom: {
      maxScale: 3,
      step: 0.5,
    },
  },
  // Slideshow settings
  Slideshow: {
    autoStart: false,
    speed: 3000,
  },
  // Carousel settings
  Carousel: {
    infinite: true,
    transition: 'slide',
    preload: 3,
  },
});
```

### 📱 **Mobile Optimization**

- Touch gestures untuk zoom, pan, dan navigation
- Responsive toolbar layout
- Optimized untuk semua screen sizes
- Touch-friendly button sizes

### 🌙 **Dark Mode Integration**

- Automatic detection dari theme aplikasi
- Custom colors untuk dark mode
- Consistent dengan design system aplikasi

### 🔗 **Data Attributes**

```html
<!-- Individual project gallery -->
<a href="image.jpg" data-fancybox="gallery-1" data-caption="Project Name - Image Type">
  <!-- All projects gallery -->
  <a href="image.jpg" data-fancybox="all-projects" data-caption="Project Name - Project Image">
    <!-- All materials gallery -->
    <a href="image.jpg" data-fancybox="all-materials" data-caption="Project Name - Submission Notes"></a></a
></a>
```

### 🎯 **Key Benefits**

1. **Professional Look**: Modern, clean interface
2. **Better UX**: Intuitive controls dan smooth animations
3. **Feature Rich**: Lengkap dengan semua tools yang dibutuhkan
4. **Performance**: Optimized loading dan caching
5. **Accessibility**: Keyboard navigation dan responsive design
6. **Customizable**: Easy to modify colors dan behavior

### 🚀 **Usage**

Cukup click pada gambar mana saja untuk membuka Fancybox modal dengan semua fitur lengkap yang tersedia!
