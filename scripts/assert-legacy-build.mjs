import fs from 'node:fs/promises';
if(await fs.access(new URL('../content-source/atomic-conversion-result.json',import.meta.url)).then(()=>true,()=>false)){
 throw new Error('This V3 builder is superseded by the Atomic conversion. Reconcile current WordPress exports before any rebuild.');
}
