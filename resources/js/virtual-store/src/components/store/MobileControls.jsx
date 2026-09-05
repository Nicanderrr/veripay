import { Joystick } from 'react-joystick-component';

export default function MobileControls({ setMove }) {
    return (
        <div className="vs-mobile-stick">
            <Joystick
                size={86}
                baseColor="rgba(17, 24, 39, 0.42)"
                stickColor="rgba(255, 255, 255, 0.9)"
                move={(event) => setMove({ x: event.x || 0, y: event.y || 0 })}
                stop={() => setMove({ x: 0, y: 0 })}
            />
        </div>
    );
}
