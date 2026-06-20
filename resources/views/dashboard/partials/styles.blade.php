    <style>
        #map {
            height: 180px;
        }

        #checkoutMap {
            height: 170px;
            min-height: 170px;
            width: 100%;
            border-radius: 6px;
            overflow: hidden;
            background: #e5e7eb;
        }

        .checkout-marker {
            align-items: center;
            border: 2px solid #ffffff;
            border-radius: 9999px;
            box-shadow: 0 5px 14px rgb(15 23 42 / 0.28);
            color: #ffffff;
            display: flex;
            font-size: 12px;
            height: 24px;
            justify-content: center;
            width: 24px;
        }

        .checkout-marker-start {
            background: #2563eb;
        }

        .checkout-marker-current {
            background: #16a34a;
        }

        .checkout-distance-label {
            background: #0f172a;
            border: 1px solid rgb(255 255 255 / 0.72);
            border-radius: 9999px;
            box-shadow: 0 6px 18px rgb(15 23 42 / 0.28);
            color: #ffffff;
            font-size: 10px;
            font-weight: 700;
            line-height: 1;
            padding: 5px 8px;
            white-space: nowrap;
        }

        @media (min-width: 640px) {
            #akuImage {
                max-width: 300px;
                aspect-ratio: 1/1;
            }
        }

        .divImage {
            scroll-snap-type: x var(--tw-scroll-snap-strictness);
        }

        .clicked {
            transform: scale(0.95);
        }

        @keyframes hanging-wiggle {
            0% {
                transform: rotate(-15deg);
            }

            50% {
                transform: rotate(-10deg);
            }

            100% {
                transform: rotate(-15deg);
            }
        }

        @keyframes hanging-wiggle2 {
            0% {
                transform: rotate(10deg);
            }

            50% {
                transform: rotate(15deg);
            }

            100% {
                transform: rotate(10deg);
            }
        }

        .hanging {
            position: absolute;
            top: 0;
            transform-origin: top center;
            /* Makes it swing from the top */
            animation: hanging-wiggle 2s ease-in-out infinite alternate;
        }

        .hanging2 {
            position: absolute;
            top: 0;
            transform-origin: top center;
            /* Makes it swing from the top */
            animation: hanging-wiggle2 2.5s ease-in-out infinite alternate;
        }

        #prayerContainer {
            transition: opacity 0.25s ease, transform 0.25s ease;
        }

        #prayerContainer.hidden {
            opacity: 0;
            transform: scale(0.96);
            pointer-events: none;
        }
    </style>