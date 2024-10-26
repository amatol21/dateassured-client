export function intToBytes(i: number): Uint8Array
{
    return Uint8Array.of(
        (i&0xff000000)>>24,
        (i&0x00ff0000)>>16,
        (i&0x0000ff00)>> 8,
        (i&0x000000ff)>> 0);
}