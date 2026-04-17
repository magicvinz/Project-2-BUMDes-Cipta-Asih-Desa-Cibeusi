const express = require('express');
const cors = require('cors');
const { Client, LocalAuth, MessageMedia } = require('whatsapp-web.js');
const qrcode = require('qrcode-terminal');

const app = express();
const port = 3000;

app.use(cors());
app.use(express.json());

const client = new Client({
    authStrategy: new LocalAuth({ dataPath: './wa_session' }),
    puppeteer: {
        args: ['--no-sandbox', '--disable-setuid-sandbox'],
        executablePath: 'C:\\Program Files (x86)\\Microsoft\\Edge\\Application\\msedge.exe' // Fallback to Edge
    }
});

let isReady = false;

client.on('qr', (qr) => {
    console.log('SCAN QR CODE INI UNTUK LOGIN:');
    qrcode.generate(qr, { small: true });
});

client.on('ready', () => {
    isReady = true;
    console.log('🤖 WhatsApp Bot is ready!');
});

client.on('authenticated', () => {
    console.log('WhatsApp Authenticated');
});

client.on('auth_failure', msg => {
    console.error('AUTHENTICATION FAILURE', msg);
});

client.on('disconnected', (reason) => {
    isReady = false;
    console.log('Client was logged out', reason);
});

(async () => {
    try {
        await client.initialize();
    } catch (e) {
        console.error("Gagal melakukan inisialisasi Client! Error: ", e);
    }
})();

// Endpoint untuk cek status
app.get('/status', (req, res) => {
    if (isReady) {
        res.json({ status: 'ready', message: 'WhatsApp Bot is ready and connected.' });
    } else {
        res.json({ status: 'not_ready', message: 'WhatsApp Bot is not ready yet.' });
    }
});

// Endpoint untuk kirim pesan
app.post('/send', async (req, res) => {
    if (!isReady) {
        return res.status(503).json({ success: false, error: 'Bot is not ready or disconnected' });
    }

    const { number, message, mediaUrl } = req.body;

    if (!number || !message) {
        return res.status(400).json({ success: false, error: 'Number and message are required' });
    }

    try {
        // Format nomor: hilangkan 0 atau + di depan, ganti dengan 62
        let formattedNumber = number.toString().replace(/\D/g, '');
        if (formattedNumber.startsWith('0')) {
            formattedNumber = '62' + formattedNumber.slice(1);
        }

        const chatId = formattedNumber + '@c.us';

        let response;
        if (mediaUrl) {
            try {
                const media = await MessageMedia.fromUrl(mediaUrl, { unsafeMime: true });
                response = await client.sendMessage(chatId, media, { caption: message });
            } catch (mediaError) {
                console.error('Error fetching media from URL:', mediaError);
                response = await client.sendMessage(chatId, message + '\n\n(Link media: ' + mediaUrl + ')');
            }
        } else {
            response = await client.sendMessage(chatId, message);
        }

        res.json({ success: true, response: response.id._serialized });
    } catch (error) {
        console.error('Error sending message:', error);
        res.status(500).json({ success: false, error: error.message });
    }
});

app.listen(port, () => {
    console.log(`🚀 WA Gateway running on http://localhost:${port}`);
    console.log('Menunggu inisialisasi client WhatsApp...');
});
