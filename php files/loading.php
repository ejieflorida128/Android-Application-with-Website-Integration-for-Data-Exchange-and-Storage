<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loading to add data</title>
    <style>
        *,
*:after,
*:before {
  box-sizing: border-box;
  transform-style: preserve-3d;
  touch-action: none;
}

:root {
  --rotation-y: 0;
  --rotation-x: 0;
	--size: 80vmin;
	--segment: calc(var(--size) / 100);
	--loading-speed: 10s;
	--color: hsl(210 80% 50%);
  --total-length: 400;
	--segments-per-second: calc(var(--loading-speed) / var(--total-length));
}

body {
  display: grid;
  place-items: center;
  min-height: 100vh;
  background: hsl(0 0% 90%);
  font-family:  'Google Sans', sans-serif, system-ui;
}

#flip:checked ~ .container {
	--rotation-y: -24;
	--rotation-x: -24;
}

[for] {
	transform: translateZ(200vmin);
	position: fixed;
	inset: 0;
}

.sr-only {
  position: absolute;
  width: 1px;
  height: 1px;
  padding: 0;
  margin: -1px;
  overflow: hidden;
  clip: rect(0, 0, 0, 0);
  white-space: nowrap;
  border-width: 0;
}

.loading-label {
	position: absolute;
	left: 0;
	bottom: 110%;
	font-weight: bold;
	font-size: clamp(1rem, var(--size) * 0.025, 6rem);
}

.container {
	width: var(--size);
	aspect-ratio: 16 / 1.25;
	position: relative;
}

.scene {
	height: 100%;
  width: 100%;
  transform: translate3d(0, 0, 100vmin) rotateX(calc(var(--rotation-y, 0) * 1deg)) rotateY(calc(var(--rotation-x, 0) * 1deg));
  transition: transform 0.25s;
}

h1 {
  opacity: 0.5;
  color: var(--color);
  font-size: calc(var(--depth, 20vmin) * 0.25);
  position: fixed;
  bottom: 1rem;
  right: 1rem;
  margin: 0;
}

.bar {
	width: 100%;
	height: 100%;
	display: grid;
	grid-template-columns: var(--columns);
}

.bar__segment {
	background: hsl(0 0% 100%);
	transform: translateZ(calc(var(--depth) * var(--segment)));
	border: calc(var(--segment) * 0.5) solid black;
}

.bar__segment:after {
	content: "";
	position: absolute;
	inset: 0;
	background: var(--color);
	transform-origin: 0 50%;
/* 	animation: reveal both linear; */
	animation-name: var(--name);
  animation-duration: var(--loading-speed);
/*   animation-duration: calc(var(--length) * var(--segments-per-second)); */
/*   animation-delay: calc(var(--delay) * var(--segments-per-second)); */
	animation-fill-mode: both;
	animation-timing-function: linear;
	animation-iteration-count: infinite;
}

@keyframes reveal {
	from {
		transform: scaleX(0);
	}
}

.bar__segment:not(:first-of-type, :last-of-type) {
	border-left: transparent;
	border-right: transparent;
}

.bar__segment:first-of-type {
	border-right: transparent;
}
.bar__segment:last-of-type {
	border-left: transparent;
}

.bar__segment:not(.bar__segment--aligned) {
	width: calc(var(--segment) * var(--length));
	transform-origin: 0 50%;
	filter: brightness(0.78);
	transform: translateZ(calc(var(--depth) * var(--segment))) rotateY(var(--rotation, 0deg));
}

.bar__segment--front {
	--rotation: -90deg;
}

.bar__segment--back {
	--rotation: 90deg;
}

/* Perhaps it makes more sense for each segment to have its own keyframe */
/* We know how each keyframes work... */
/**
* 1. Start at 0% plus delay% with scaleX(0). We can normalize the keyframes by dividing 100 / 400 to get a keyframe step.
* 2. Then by the length%, 100% scaleX(1). 
* 3. That's it. Then we don't need delays. Set the animation-name and forget.
* */
/* Keyframes Step = 100 / 400 = 0.25 */
/**
* Step: 100 / 400 = 0.25% ;
* Start: --delay * step || previous ending frame;
* End: (--delay + --length) * step || The next --delay * step;
* */
@keyframes segment-1 {
  0% {
    transform: scaleX(0);
  }
  5%, 100% {
    transform: scaleX(1);
  }

}
@keyframes segment-2 {
  0%, 5% {
    transform: scaleX(0);
  }
  12.5%, 100% {
    transform: scaleX(1);
  }
}
@keyframes segment-3 {
  0%, 12.5% {
    transform: scaleX(0);
  }
  15%, 100% {
    transform: scaleX(1);
  }
}
@keyframes segment-4 {
  0%, 15% {
    transform: scaleX(0);
  }
  27.5%, 100% {
    transform: scaleX(1);
  }
}
@keyframes segment-5 {
  0%, 27.5% {
    transform: scaleX(0);
  }
  30%, 100% {
    transform: scaleX(1);
  }
}
@keyframes segment-6 {
  0%, 30% {
    transform: scaleX(0);
  }
  45%, 100% {
    transform: scaleX(1);
  }
}
@keyframes segment-7 {
  0%, 45% {
    transform: scaleX(0);
  }
  47.5%, 100% {
    transform: scaleX(1);
  }
}
@keyframes segment-8 {
  0%, 47.5% {
    transform: scaleX(0);
  }
  65%, 100% {
    transform: scaleX(1);
  }
}
@keyframes segment-9 {
  0%, 65% {
    transform: scaleX(0);
  }
  70%, 100% {
    transform: scaleX(1);
  }
}
@keyframes segment-10 {
  0%, 70% {
    transform: scaleX(0);
  }
  82.5%, 100% {
    transform: scaleX(1);
  }
}
@keyframes segment-11 {
  0%, 82.5% {
    transform: scaleX(0);
  }
  90%, 100% {
    transform: scaleX(1);
  }
}
@keyframes segment-12 {
  0%, 90% {
    transform: scaleX(0);
  }
  95%, 100% {
    transform: scaleX(1);
  }
}
@keyframes segment-13 {
  0%, 95% {
    transform: scaleX(0);
  }
  100% {
    transform: scaleX(1);
  }
}
    </style>
</head>
<body>

<input class="sr-only" type="checkbox" id="flip">
    <label for="flip"></label>
    <h1>tap.</h1>
    <div class="container">
      <div class="scene">
        <span class="loading-label">Loading...</span>
        <!-- Note:: You could write a quick JavaScript generator to do this -->
        <!-- Need a notion of the number of segments in the bar plus the layout for the grid -->
        <div class="bar" style="--columns: 20% 0 10% 0 10% 0 10% 0 20% 0 10% 0 20%; --total-length: 400;">
          <!-- Kind of a weird way to do it but... -->
          <!-- The --length is the percentage length of the container -->
          <!-- The --depth is a depth off the center line as a percentage of the container -->
          <!-- The aligned should add to 100 as we're doing grid -->
          <!-- The non-aligned dictate the depth being used -->
          <!-- Needs to be the length of the depth currently plus the destination depth -->
          <div style="--name:  segment-1; --delay:   0; --length: 20; --depth:   0;" class="bar__segment bar__segment--aligned"></div>
          <div style="--name:  segment-2; --delay:  20; --length: 30; --depth:   0;" class="bar__segment bar__segment--front"></div>
          <div style="--name:  segment-3; --delay:  50; --length: 10; --depth:  30;" class="bar__segment bar__segment--aligned"></div>
          <div style="--name:  segment-4; --delay:  60; --length: 50; --depth:  30;" class="bar__segment bar__segment--back"></div>
          <div style="--name:  segment-5; --delay: 110; --length: 10; --depth: -20;" class="bar__segment bar__segment--aligned"></div>
          <div style="--name:  segment-6; --delay: 120; --length: 60; --depth: -20;" class="bar__segment bar__segment--front"></div>
          <div style="--name:  segment-7; --delay: 180; --length: 10; --depth:  40;" class="bar__segment bar__segment--aligned"></div>
          <div style="--name:  segment-8; --delay: 190; --length: 70; --depth:  40;" class="bar__segment bar__segment--back"></div>
          <div style="--name:  segment-9; --delay: 260; --length: 20; --depth: -30;" class="bar__segment bar__segment--aligned"></div>
          <div style="--name: segment-10; --delay: 280; --length: 50; --depth: -30;" class="bar__segment bar__segment--front"></div>
          <div style="--name: segment-11; --delay: 330; --length: 30; --depth:  20;" class="bar__segment bar__segment--aligned"></div>
          <div style="--name: segment-12; --delay: 360; --length: 20; --depth:  20;" class="bar__segment bar__segment--back"></div>
          <div style="--name: segment-13; --delay: 380; --length: 20; --depth:   0;" class="bar__segment bar__segment--aligned"></div>
        </div>
      </div>
    </div>


    <script>
        // Function to redirect after a delay
        function redirectAfterDelay(url, delay) {
            setTimeout(function() {
                window.location.href = url;
            }, delay);
        }

        // Call the function with the desired URL and delay time (in milliseconds)
        redirectAfterDelay("../SQLITEMYSQLSYNC/newData.php", 9940); // 3000 milliseconds = 3 seconds
    </script>
    
</body>
</html>