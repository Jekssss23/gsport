import React, { useEffect, useRef, useMemo } from 'react';
import './LoadingScreen.css';

const ROWS = 4;
const COLS = 4;
const GAP = 0.1;
const BREATHE_MS = 3000;

const rnd = (s) => {
  const x = Math.sin(s * 127.1 + 311.7) * 43758.5453;
  return x - Math.floor(x);
};

function buildPieces() {
  const list = [];
  for (let r = 0; r < ROWS; r++) {
    for (let c = 0; c < COLS; c++) {
      const i = r * COLS + c;
      const ang = rnd(i * 17 + 1) * 360;
      const dst = 300 + rnd(i * 31 + 7) * 200;
      const sx = (Math.cos((ang * Math.PI) / 180) * dst) | 0;
      const sy = (Math.sin((ang * Math.PI) / 180) * dst) | 0;
      const rz = ((rnd(i * 53 + 13) - 0.5) * 140) | 0;
      const rx = ((rnd(i * 71 + 29) - 0.5) * 50) | 0;
      const ry = ((rnd(i * 97 + 43) - 0.5) * 50) | 0;

      list.push({
        i,
        clipPath: `inset(${((r / ROWS) * 100).toFixed(2)}% ${(((COLS - c - 1) / COLS) * 100).toFixed(2)}% ${(((ROWS - r - 1) / ROWS) * 100).toFixed(2)}% ${((c / COLS) * 100).toFixed(2)}%)`,
        scattered: `translate3d(${sx}px,${sy}px,60px) rotateX(${rx}deg) rotateY(${ry}deg) rotate(${rz}deg) scale(.3)`,
      });
    }
  }
  list.sort((a, b) => rnd(a.i * 7 + 3) - rnd(b.i * 7 + 3));
  return list;
}

const CENTER = 'translate3d(0,0,0) rotateX(0) rotateY(0) rotate(0) scale(1)';

/**
 * Looping "lego assembly" loading screen.
 *
 * Usage:
 *   import LoadingScreen from './LoadingScreen';
 *   <LoadingScreen logo={require('./LOGO (GSC).png')} />
 *   // or
 *   <LoadingScreen logo="/assets/logo.png" />
 */
export default function LoadingScreen({ logo, size = 200 }) {
  const stageRef = useRef(null);
  const elRefs = useRef([]);
  const pieces = useMemo(buildPieces, []);
  const timers = useRef([]);

  const later = (fn, ms) => {
    const id = setTimeout(fn, ms);
    timers.current.push(id);
  };

  useEffect(() => {
    const els = elRefs.current;
    if (!els.length) return;

    /* initial scattered state */
    els.forEach((el, idx) => {
      if (!el) return;
      el.style.transform = pieces[idx].scattered;
      el.style.opacity = '0';
    });
    void stageRef.current?.offsetWidth;

    function assemble() {
      els.forEach((el, n) => {
        if (!el) return;
        const d = `${(0.3 + n * GAP).toFixed(2)}s`;
        el.style.transition = `transform .5s cubic-bezier(.34,1.56,.64,1) ${d}, opacity .5s cubic-bezier(.34,1.56,.64,1) ${d}`;
        el.style.transform = CENTER;
        el.style.opacity = '1';
      });

      const doneMs = (0.3 + (els.length - 1) * GAP + 0.5) * 1000 + 100;
      later(() => {
        if (stageRef.current)
          stageRef.current.style.animation = `ls-breathe ${BREATHE_MS}ms ease-in-out infinite`;
        later(() => {
          if (stageRef.current) stageRef.current.style.animation = 'none';
          scatter();
        }, BREATHE_MS);
      }, doneMs);
    }

    function scatter() {
      const order = els.map((el, idx) => ({ el, idx })).reverse();
      order.forEach(({ el, idx }, n) => {
        if (!el) return;
        const d = `${(n * GAP * 0.7).toFixed(2)}s`;
        el.style.transition = `transform .4s cubic-bezier(.55,0,1,.45) ${d}, opacity .4s cubic-bezier(.55,0,1,.45) ${d}`;
        el.style.transform = pieces[idx].scattered;
        el.style.opacity = '0';
      });

      const doneMs = ((els.length - 1) * GAP * 0.7 + 0.4) * 1000 + 600;
      later(assemble, doneMs);
    }

    assemble();
    return () => timers.current.forEach(clearTimeout);
  }, [pieces]);

  return (
    <div className="ls-root">
      <div
        className="ls-stage"
        ref={stageRef}
        style={{ width: size, height: size }}
      >
        {pieces.map((p, idx) => (
          <div
            key={p.i}
            className="ls-piece"
            ref={(el) => (elRefs.current[idx] = el)}
            style={{ clipPath: p.clipPath }}
          >
            <img src={logo} alt="" draggable={false} />
          </div>
        ))}
      </div>
    </div>
  );
}
