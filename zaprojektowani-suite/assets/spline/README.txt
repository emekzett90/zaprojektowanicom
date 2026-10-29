Original scene: https://prod.spline.design/kZDDjO5HuC9GJUM2/scene.splinecode
Viewer: @splinetool/viewer 1.10.57 (unchanged)
Original scene format 114, migrated to 117 with that viewer's own migration and MessagePack serializer.
Geometry, materials, objects, camera, events and animation data are unchanged.
Added only the vendor default fields for API/webhooks, videoStatic and pixel ratios.
This removes per-load migration and its updating-from warning without changing the console API.
