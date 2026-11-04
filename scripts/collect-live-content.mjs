import fs from 'node:fs/promises';
import path from 'node:path';
const root = path.resolve('content-source');
await fs.mkdir(root, {recursive:true});
const failures=[]; const records=[]; const assets=new Set();
const decode=s=>s.replace(/&#(\d+);/g,(_,n)=>String.fromCodePoint(+n)).replace(/&#x([0-9a-f]+);/gi,(_,n)=>String.fromCodePoint(parseInt(n,16))).replace(/&amp;/g,'&').replace(/&nbsp;/g,' ').replace(/&quot;/g,'"').replace(/&lt;/g,'<').replace(/&gt;/g,'>');
const plain=s=>decode(s.replace(/<(script|style)\b[^>]*>[\s\S]*?<\/\1>/gi,'').replace(/<\/(p|div|h[1-6]|li|tr)>/gi,'\n').replace(/<br\s*\/?\s*>/gi,'\n').replace(/<[^>]+>/g,'')).replace(/[ \t]+/g,' ').replace(/\n\s*\n/g,'\n\n').trim();
async function get(url){const r=await fetch(url,{signal:AbortSignal.timeout(45000)});if(!r.ok)throw Error(`${r.status} ${url}`);return r;}
for(const base of ['https://mpcbc.net','https://mpcbc.net/english']){
  const site=base.endsWith('english')?'english':'main';
  for(const type of ['pages','posts','categories','media']){
    const all=[];
    try{
      let total=1;
      for(let p=1;p<=total;p++){
        const url=`${base}/wp-json/wp/v2/${type}?per_page=100&page=${p}`;
        const r=await get(url);total=+(r.headers.get('x-wp-totalpages')||1);const raw=await r.text();
        let data;try{data=JSON.parse(raw);}catch{const start=raw.indexOf('[{"id":');if(start<0)throw Error(`Invalid API JSON: ${url}`);data=JSON.parse(raw.slice(start));}
        all.push(...data);
      }
      await fs.writeFile(path.join(root,`${site}-${type}.json`),JSON.stringify(all,null,2));
      if(['pages','posts'].includes(type))for(const p of all){
        const html=p.content.rendered;
        const links=[...html.matchAll(/(?:href|src)=["']([^"']+)/g)].map(m=>decode(m[1]));
        links.forEach(x=>assets.add(x));
        records.push({site,type,id:p.id,url:p.link,title:plain(p.title.rendered),date:p.date,modified:p.modified,text:plain(html),links,html});
      }
      if(type==='media')all.forEach(m=>assets.add(m.source_url));
      console.log(`${site} ${type}: ${all.length}`);
    }catch(e){failures.push({site,type,error:String(e)});console.log(String(e));}
  }
}
for(const rec of records){try{const html=await(await get(rec.url)).text();const name=`${rec.site}-${rec.type}-${rec.id}`;await fs.writeFile(path.join(root,`${name}.html`),html);for(const m of html.matchAll(/(?:href|src)=["']([^"']+)/g))assets.add(decode(m[1]));}catch(e){failures.push({url:rec.url,error:String(e)});}}
await fs.writeFile(path.join(root,'content-index.json'),JSON.stringify(records,null,2));
await fs.writeFile(path.join(root,'asset-links.json'),JSON.stringify([...assets].sort(),null,2));
await fs.writeFile(path.join(root,'collection-report.json'),JSON.stringify({collectedAt:new Date().toISOString(),records:records.length,assets:assets.size,failures},null,2));
await fs.writeFile(path.join(root,'CONTENT.md'),records.map(r=>`# ${r.title}\n\nSource: ${r.url}\nPublished: ${r.date}; modified: ${r.modified}\n\n${r.text}\n\nLinks:\n${r.links.map(l=>'- '+l).join('\n')}\n`).join('\n---\n\n'));
console.log(JSON.stringify({records:records.length,assets:assets.size,failures}));
