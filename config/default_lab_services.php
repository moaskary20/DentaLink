<?php

/**
 * Default lab services from Khazaf Dental Lab price list.
 * Each new lab receives these services automatically; existing labs can be backfilled via artisan.
 */
return [
    // Crown & CAD/CAM
    ['name' => 'Full Zirconia', 'category' => 'Crown', 'price' => 200, 'turnaround_days' => 4],
    ['name' => 'Core Zirconia Layered', 'category' => 'Crown', 'price' => 250, 'turnaround_days' => 4],
    ['name' => 'Emax Layered', 'category' => 'Crown', 'price' => 250, 'turnaround_days' => 4],
    ['name' => 'Emax Pressed and Glazed', 'category' => 'Crown', 'price' => 200, 'turnaround_days' => 4],
    ['name' => 'PFM Crown', 'category' => 'Crown', 'price' => 150, 'turnaround_days' => 4],
    ['name' => 'Model Scan to STL file (Per Case Scan/Each Jaw)', 'category' => 'Crown', 'price' => 100, 'turnaround_days' => 4],
    ['name' => 'Design/Mockup (per Jaw)', 'category' => 'Crown', 'price' => 150, 'turnaround_days' => 4],
    ['name' => 'Printing (Per Jaw)', 'category' => 'Crown', 'price' => 75, 'turnaround_days' => 4],
    ['name' => 'Printing (Upper & Lower)', 'category' => 'Crown', 'price' => 125, 'turnaround_days' => 4],
    ['name' => 'Printing (Upper & Lower With Articulator)', 'category' => 'Crown', 'price' => 150, 'turnaround_days' => 4],
    ['name' => 'Abutment Preparation', 'category' => 'Crown', 'price' => 30, 'turnaround_days' => 4],
    ['name' => 'Cementation', 'category' => 'Crown', 'price' => 20, 'turnaround_days' => 4],
    ['name' => 'Study Model (Upper and Lower)', 'category' => 'Crown', 'price' => 100, 'turnaround_days' => 4],

    // Acrylic RPD / Flexible Denture
    ['name' => 'Flexible Partial Denture 1 to 2 Units', 'category' => 'Denture', 'price' => 450, 'turnaround_days' => 4],
    ['name' => 'Flexible Partial Denture 3 to 6 Units', 'category' => 'Denture', 'price' => 500, 'turnaround_days' => 4],
    ['name' => 'Flexible Partial Denture 7+ Units', 'category' => 'Denture', 'price' => 650, 'turnaround_days' => 4],
    ['name' => 'Flexible Full Denture', 'category' => 'Denture', 'price' => 750, 'turnaround_days' => 4],
    ['name' => 'Acrylic Partial Denture 1 to 2 Units', 'category' => 'Denture', 'price' => 150, 'turnaround_days' => 4],
    ['name' => 'Acrylic Partial Denture 3 to 6 Units', 'category' => 'Denture', 'price' => 250, 'turnaround_days' => 4],
    ['name' => 'Acrylic Full Denture', 'category' => 'Denture', 'price' => 450, 'turnaround_days' => 4],
    ['name' => 'Full Acrylic Denture 7+ Units', 'category' => 'Denture', 'price' => 350, 'turnaround_days' => 4],
    ['name' => 'Hybrid Denture Over Implant With Metal Bar', 'category' => 'Denture', 'price' => 800, 'turnaround_days' => 4],
    ['name' => 'Hybrid Denture Over Implant', 'category' => 'Denture', 'price' => 600, 'turnaround_days' => 4],
    ['name' => 'Bite Rim', 'category' => 'Denture', 'price' => 50, 'turnaround_days' => 4],
    ['name' => 'Denture Reline', 'category' => 'Denture', 'price' => 120, 'turnaround_days' => 4],
    ['name' => 'Denture Repair', 'category' => 'Denture', 'price' => 75, 'turnaround_days' => 4],
    ['name' => 'Denture Add Tooth', 'category' => 'Denture', 'price' => 50, 'turnaround_days' => 4],
    ['name' => 'Special Tray', 'category' => 'Denture', 'price' => 50, 'turnaround_days' => 4],

    // Orthodontic & Hard Acrylic Appliances
    ['name' => 'Lower Lingual Arch', 'category' => 'Orthodontic', 'price' => 150, 'turnaround_days' => 4],
    ['name' => 'Nance Appliances', 'category' => 'Orthodontic', 'price' => 150, 'turnaround_days' => 4],
    ['name' => 'Space Maintainer', 'category' => 'Orthodontic', 'price' => 100, 'turnaround_days' => 4],
    ['name' => 'Habit Breaker Appliance', 'category' => 'Orthodontic', 'price' => 150, 'turnaround_days' => 4],
    ['name' => 'Thumb Sucking Appliance', 'category' => 'Orthodontic', 'price' => 150, 'turnaround_days' => 4],
    ['name' => 'Repair Appliance', 'category' => 'Orthodontic', 'price' => 50, 'turnaround_days' => 4],
    ['name' => 'Michigan Splint', 'category' => 'Orthodontic', 'price' => 300, 'turnaround_days' => 4],
    ['name' => 'Hawley Retainer (Per Jaw)', 'category' => 'Orthodontic', 'price' => 200, 'turnaround_days' => 4],
    ['name' => 'Hyrax Expansion', 'category' => 'Orthodontic', 'price' => 250, 'turnaround_days' => 4],
    ['name' => 'Transpalatal Arch', 'category' => 'Orthodontic', 'price' => 100, 'turnaround_days' => 4],
    ['name' => 'Twin Block Appliances', 'category' => 'Orthodontic', 'price' => 250, 'turnaround_days' => 4],
    ['name' => 'Anderson Appliances', 'category' => 'Orthodontic', 'price' => 250, 'turnaround_days' => 4],
    ['name' => 'Frankel Appliances', 'category' => 'Orthodontic', 'price' => 300, 'turnaround_days' => 4],
    ['name' => 'Soft Night Guard (Per Jaw)', 'category' => 'Orthodontic', 'price' => 100, 'turnaround_days' => 4],
    ['name' => 'Acrylic Occlusal Splint', 'category' => 'Orthodontic', 'price' => 250, 'turnaround_days' => 4],
    ['name' => 'Half Hard / Half Soft Night Guard', 'category' => 'Orthodontic', 'price' => 200, 'turnaround_days' => 4],
    ['name' => 'Essix Retainer', 'category' => 'Orthodontic', 'price' => 100, 'turnaround_days' => 4],
    ['name' => 'Bleaching Tray', 'category' => 'Orthodontic', 'price' => 100, 'turnaround_days' => 4],
    ['name' => 'Snap On (One Jaw)', 'category' => 'Orthodontic', 'price' => 300, 'turnaround_days' => 4],
    ['name' => 'Snap On (Upper and Lower)', 'category' => 'Orthodontic', 'price' => 500, 'turnaround_days' => 4],
];
