# Hero video

Remotion project that renders the muted, looping background video for the
homepage hero (both designs). No text is baked in: the headline and the dark
overlay are set in Elementor.

Version 1 uses only VBS *Magnified!* 2025 footage. It is a stand-in until
Sunday footage (worship, welcome, fellowship) is filmed. Check that a
photo/video release covers the children before it goes on the public site.

## Footage

Source files live in `public/footage/` and are git-ignored. Copy them in before
rendering:

- `public/footage/vbs-2025.mp4` — "VBS - Magnified! 2025 MPCBC.mp4" (1080p)

## Edit

`src/HeroLoop.tsx` holds the timeline: 10 shots of 2.5 s, 10-frame crossfades (22 s).
`trimBefore` is the source start frame (seconds × 30). The first shot starts
20 frames into the gathering shot and the "Loop back" tail plays those 20
frames, so the end joins the start without a jump. If you change a shot's
length or add one, update `durationInFrames` in `src/Root.tsx`.

Preview: `npm run dev`

## Render

```
npx remotion render HeroLoop out/hero-1080.mp4 --codec=h264 --crf=32 --x264-preset=slow --muted
npx remotion render HeroLoop out/hero-720.mp4 --codec=h264 --crf=30 --x264-preset=slow --width=1280 --height=720 --muted
npx remotion still HeroLoop out/hero-poster.jpg --frame=0 --jpeg-quality=80
```

Upload the MP4 to the WordPress Media Library and set it as the hero
container's background video (loop on, play on mobile on), with the poster as the
fallback image.
