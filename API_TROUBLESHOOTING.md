# API Connection Troubleshooting

## Problem: Network Request Failed

### Symptoms:
- Error: `TypeError: Network request failed`
- API calls to attendance settings return null
- Mobile app cannot connect to backend

### Solutions:

#### 1. Check IP Address
Update the IP address in `App/src/config/api.js`:
```javascript
const getLocalIP = () => {
  if (__DEV__) {
    return 'http://YOUR_COMPUTER_IP'; // Replace with your actual IP
  }
  return 'http://YOUR_COMPUTER_IP';
};
```

To find your IP:
- Windows: Open Command Prompt → `ipconfig`
- Look for "IPv4 Address" (usually 192.168.x.x or 10.x.x.x)

#### 2. Check Apache Server
- Make sure XAMPP Apache is running
- Check port 80 is not blocked
- Test in browser: `http://YOUR_IP/g_sports_center_admin_robby/api/attendance/settings`

#### 3. Network Configuration
- Ensure mobile device and computer are on same WiFi network
- Check firewall settings
- Disable VPN if active

#### 4. Test API Endpoints
```bash
# Test settings endpoint
curl http://YOUR_IP/g_sports_center_admin_robby/api/attendance/settings

# Should return JSON like:
{"success":true,"data":{"location_name":"G Sports Center",...}}
```

#### 5. Debug Mode
Enable debug logging in mobile app:
- Check console logs for API calls
- Verify correct URL is being called
- Check response status codes

### Common Issues:
1. **Wrong IP**: Using localhost instead of actual IP
2. **Port blocked**: Apache not running or firewall blocking
3. **Different networks**: Phone and computer on different WiFi
4. **VPN**: VPN interfering with local network

### Expected Response:
When working correctly, API should return:
```json
{
  "success": true,
  "data": {
    "id": "1",
    "location_name": "G Sports Center",
    "latitude": "-6.20000000",
    "longitude": "106.81666600",
    "radius_meters": "100",
    "is_active": "1"
  }
}
```

### Current Configuration:
- API Base URL: `http://10.73.31.124/g_sports_center_admin_robby/api`
- Settings Endpoint: `/attendance/settings`
- Timeout: 10 seconds
- CORS: Enabled
