interface LocationInfo {
    ip: string | null;
    city: string | null;
    country: string | null;
    region: string | null;
}

export function getDeviceName(): string {
    const ua = navigator.userAgent.toLowerCase();
    let device = "Unknown Device";
    let browser = "Unknown Browser";

    // Detect device
    if (ua.includes('iphone')) device = "iPhone";
    else if (ua.includes('ipad')) device = "iPad";
    else if (ua.includes('ipod')) device = "iPod";
    else if (ua.includes('macintosh')) device = "Mac";
    else if (ua.includes('android')) device = "Android Device";
    else if (ua.includes('windows phone')) device = "Windows Phone";
    else if (ua.includes('windows')) device = "Windows PC";
    else if (ua.includes('linux')) device = "Linux Machine";

    // Detect browser
    if (ua.includes('edg')) browser = "Edge";
    else if (ua.includes('chrome') && !ua.includes('edg')) browser = "Chrome";
    else if (ua.includes('firefox')) browser = "Firefox";
    else if (ua.includes('safari') && !ua.includes('chrome')) browser = "Safari";

    // Combine nicely
    return `${browser} di ${device}`;
}

export async function getLocationInfo(): Promise<LocationInfo> {
    try {
        const response = await fetch('https://ipapi.co/json/');
        const data = await response.json();
        return {
            ip: data.ip,
            city: data.city,
            country: data.country_name,
            region: data.region,
        };
    } catch (error) {
        console.error('Failed to fetch location', error);
        return {
            ip: null,
            city: null,
            country: null,
            region: null,
        };
    }
}
