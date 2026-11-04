import { Composition } from "remotion";
import { HeroLoop } from "./HeroLoop";

export const RemotionRoot: React.FC = () => {
  return (
    <Composition
      id="HeroLoop"
      component={HeroLoop}
      // 10 shots x 75 + 20-frame tail - 10 crossfades x 10
      durationInFrames={670}
      fps={30}
      width={1920}
      height={1080}
    />
  );
};
