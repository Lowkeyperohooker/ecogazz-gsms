<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>EcoGazz GSMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .preloader-container {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            /* Matches the dark gradient of your Login Screen */
            background: linear-gradient(to bottom right, #1a2332, #2d4a3e, #1a2332); 
            z-index: 999999;
        }
        .logo-placeholder {
            width: 75px;
            height: 75px;
            background: linear-gradient(to bottom right, #3dbb91, #2a8c6a);
            border-radius: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 8px 25px rgba(61, 187, 145, 0.3);
            animation: pulse 1.5s infinite;
            margin-bottom: 25px;
        }
        .logo-placeholder svg {
            width: 36px;
            height: 36px;
            fill: white;
        }
        .loading-text {
            color: #ffffff;
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 3px;
            text-transform: uppercase;
            animation: fade 1.5s infinite;
        }

        /* Animations */
        @keyframes pulse {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(61, 187, 145, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 15px rgba(61, 187, 145, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(61, 187, 145, 0); }
        }
        @keyframes fade {
            0%, 100% { opacity: 0.5; }
            50% { opacity: 1; }
        }
    </style>
</head>
<body class="bg-[#f0f4f3] text-[#1a2332] flex justify-center items-center h-screen overflow-hidden text-[18px]">
    
    <div id="app" class="w-full h-full">
        
        <div class="preloader-container">
            <div class="logo-placeholder" id="logo-image">
                <svg viewBox="0 0 512 512">
                    <path d="M336 448H16c-8.8 0-16 7.2-16 16v32c0 8.8 7.2 16 16 16h320c8.8 0 16-7.2 16-16v-32c0-8.8-7.2-16-16-16zm157.2-340.7l-81-85.5c-4.4-4.6-10.5-7.3-17-7.3H32C14.3 14.5 0 28.8 0 46.5v339c0 17.7 14.3 32 32 32h320c17.7 0 32-14.3 32-32V256h10.2l53.4 56.4c5.6 5.9 8.7 13.8 8.7 22V448c0 17.7 14.3 32 32 32s32-14.3 32-32V202.5c0-17.6-6.8-34.6-19.1-47.8zM256 128H96c-8.8 0-16-7.2-16-16V80c0-8.8 7.2-16 16-16h160c8.8 0 16 7.2 16 16v32c0 8.8-7.2 16-16 16z"/>
                </svg>
            </div>
            <div class="loading-text">Starting Ecogazz</div>
        </div>

    </div>

</body>
</html>