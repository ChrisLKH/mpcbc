import { Video } from "@remotion/media";
import { linearTiming, TransitionSeries } from "@remotion/transitions";
import { fade } from "@remotion/transitions/fade";
import { AbsoluteFill, interpolate, staticFile, useCurrentFrame } from "remotion";

// Muted background loop for the homepage hero. No text: the headline lives in Elementor.
// Each shot is 75 frames (2.5s), joined by 10-frame crossfades. Footage times are source frames at 30fps.

const VBS_2025 = staticFile("footage/vbs-2025.mp4");

const Shot: React.FC<{ trimBefore: number; zoomOffset?: number }> = ({
  trimBefore,
  zoomOffset = 0,
}) => {
  const frame = useCurrentFrame();

  return (
    <AbsoluteFill
      style={{
        // Slow push-in, like the reference reel.
        scale: interpolate(frame + zoomOffset, [0, 95], [1, 1.06], {
          extrapolateLeft: "clamp",
          extrapolateRight: "clamp",
        }),
        // Calm the very saturated phone footage so it sits under a dark overlay.
        filter: "saturate(0.82) contrast(1.06) brightness(0.96)",
      }}
    >
      <Video src={VBS_2025} trimBefore={trimBefore} muted objectFit="cover" />
    </AbsoluteFill>
  );
};

const crossfade = (
  <TransitionSeries.Transition
    presentation={fade()}
    timing={linearTiming({ durationInFrames: 10 })}
  />
);

export const HeroLoop: React.FC = () => {
  return (
    <AbsoluteFill style={{ backgroundColor: "black" }}>
      <TransitionSeries name="Hero timeline">
        {/* Starts 20 frames into the gathering shot; the "Loop back" tail plays those 20 frames, so the loop is seamless.
            Don't use the source's opening sanctuary shot (0-11.8s): the recap's editor put a flashing sparkle-grid effect on it. */}
        <TransitionSeries.Sequence name="Gathering" durationInFrames={75}>
          <Shot trimBefore={725} zoomOffset={20} />
        </TransitionSeries.Sequence>
        {crossfade}
        <TransitionSeries.Sequence name="Arriving" durationInFrames={75}>
          <Shot trimBefore={984} />
        </TransitionSeries.Sequence>
        {crossfade}
        <TransitionSeries.Sequence name="Teachers wave" durationInFrames={75}>
          <Shot trimBefore={3360} />
        </TransitionSeries.Sequence>
        {crossfade}
        <TransitionSeries.Sequence name="Craft table" durationInFrames={75}>
          <Shot trimBefore={1920} />
        </TransitionSeries.Sequence>
        {crossfade}
        <TransitionSeries.Sequence name="Building" durationInFrames={75}>
          <Shot trimBefore={2100} />
        </TransitionSeries.Sequence>
        {crossfade}
        <TransitionSeries.Sequence name="Smile" durationInFrames={75}>
          <Shot trimBefore={5988} />
        </TransitionSeries.Sequence>
        {crossfade}
        <TransitionSeries.Sequence name="Volunteers wave" durationInFrames={75}>
          <Shot trimBefore={6120} />
        </TransitionSeries.Sequence>
        {crossfade}
        <TransitionSeries.Sequence name="String lights" durationInFrames={75}>
          <Shot trimBefore={3939} />
        </TransitionSeries.Sequence>
        {crossfade}
        <TransitionSeries.Sequence name="Glow party" durationInFrames={75}>
          <Shot trimBefore={7560} />
        </TransitionSeries.Sequence>
        {crossfade}
        <TransitionSeries.Sequence name="Everyone" durationInFrames={75}>
          <Shot trimBefore={7950} />
        </TransitionSeries.Sequence>
        {crossfade}
        <TransitionSeries.Sequence name="Loop back" durationInFrames={20}>
          <Shot trimBefore={705} />
        </TransitionSeries.Sequence>
      </TransitionSeries>
    </AbsoluteFill>
  );
};
