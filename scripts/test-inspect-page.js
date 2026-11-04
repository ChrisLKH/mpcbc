import http from 'node:http';

http.get('http://mpcbc.local/', (res) => {
  let html = '';
  res.on('data', chunk => html += chunk);
  res.on('end', () => {
    // Extract container classes
    const containerMatches = [...html.matchAll(/class="([^"]*e-con[^"]*)"/g)].map(m => m[1]);
    console.log('Containers found:', containerMatches.length);
    console.log(containerMatches.slice(0, 10));
    
    // Check if times_card appears anywhere in html
    const idx = html.indexOf('times_card');
    console.log('times_card index:', idx);
    if (idx !== -1) {
      console.log('Context:', html.substring(idx - 100, idx + 100));
    }
  });
});
