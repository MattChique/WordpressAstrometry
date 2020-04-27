# Wordpress Astrometry.NET Plugin
Welcome to my first Wordpress Plugin. It is an Plugin, that provides a Gutenberg block, which uses astrometry.net for solving an astronomical image.

# What it does
- Solving astronomic images via [astrometry.NET](http://nova.astrometry.net)
- Colored annotations by different catalogues (HD, Bright, NGC, IC, the ones, which are delivered by astrometry.NET)
- Globally turn on Messier Objects to override NGC
- Global settings administration page: Decide wether to show the celestial grid or not. Choose colors of catalogues and grid
- All annotations are stylable via custom CSS as they are SVG objects
- Image zoom / monochrome-invert switch
- Zoomable skyplot
- Astrometry data output (RA,Dec,Field) and photografic details output (frames, exposuretime, date, equipment) below image
- Extended wordpress search (Tags like M101 are stored in customfields)
- Default localisation is english, a german localisation is also included

# How it looks
![look](https://github.com/RedburnM/WordpressAstrometry/raw/master/assets/example1.png)
![look](https://github.com/RedburnM/WordpressAstrometry/raw/master/assets/example2.png)

Or checkout [my demo](https://www.explorespace.de/2020/m45/)

# What you need
All you need is an API key. Get it here: http://nova.astrometry.net. After install, go to the plugins settings and fill it in the API key data field.

# Installation
Just download the [ZIP file](https://github.com/RedburnM/WordpressAstrometry/archive/master.zip) and upload it with your Wordpress plugin upload interface.
