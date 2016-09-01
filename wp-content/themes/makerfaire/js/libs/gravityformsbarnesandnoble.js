jQuery(document).ready(function () {
  // Initial Load 
  jQuery('#field_108_341').hide();
  jQuery('#field_108_342').hide();
  jQuery('#field_108_343').hide();

  //Barnes and Noble Store Select
  //On page load if State is selected - populate the location drop down (this happens when the prev button is used)
  if (jQuery('#input_108_378').length) {
    if (jQuery('#input_108_378').val() !== null) {
      jQuery('#field_108_341').show();
      jQuery('#field_108_342').show();
      jQuery('#field_108_343').show();

      var state = jQuery('#input_108_340').val();
      var input_108_341 = jQuery('#input_108_341').val();
      var input_108_342 = jQuery('#input_108_342').val();
      var input_108_343 = jQuery('#input_108_343').val();
      // popStoreSel(state);
      //select the previous selected store
      jQuery('#input_108_341 option[value="' + input_108_341 + '"]').attr("selected", "selected");
      jQuery('#input_108_342 option[value="' + input_108_342 + '"]').attr("selected", "selected");
      jQuery('#input_108_343 option[value="' + input_108_343 + '"]').attr("selected", "selected");
    }
  }
  //On state change, populate the location drop down
  jQuery("#input_108_378").change(function () {
    var state = jQuery(this).val();
    console.log(state);
    if (state !== null)
    {
      jQuery('#field_108_341').show();
      jQuery('#field_108_342').show();
      jQuery('#field_108_343').show();
      popStoreSel(state);
    }
  });
});


function popStoreSel(state) {
  //Clear out values
  jQuery('#input_108_341').empty();
  jQuery('#input_108_342').empty();
  jQuery('#input_108_343').empty();
  jQuery('#input_108_341').append('<option value="">Please select one.</option>');
  jQuery('#input_108_342').append('<option value="">n/a</option>');
  jQuery('#input_108_343').append('<option value="">n/a</option>');



  //// Go through State Array
  for (var i = 0; i < state.length; i++) {
    var obj = state[i];
    var stateData = eval(locationjson[obj]);
    sortJsonArrayByProperty(stateData,"name");
    //var arrayLength = stateData.length;
    if (!(typeof (stateData) === "undefined"))
    {
      jQuery.each(stateData, function (stateValue) {
        jQuery('#input_108_341').append('<option value="' + stateData[stateValue].store + '">' + stateData[stateValue].name + '</option>');
        jQuery('#input_108_342').append('<option value="' + stateData[stateValue].store + '">' + stateData[stateValue].name + '</option>');
        jQuery('#input_108_343').append('<option value="' + stateData[stateValue].store + '">' + stateData[stateValue].name + '</option>');
      });
    }

  }
}

function sortJsonArrayByProperty(objArray, prop, direction){
    if (arguments.length<2) throw new Error("sortJsonArrayByProp requires 2 arguments");
    var direct = arguments.length>2 ? arguments[2] : 1; //Default to ascending

    if (objArray && objArray.constructor===Array){
        var propPath = (prop.constructor===Array) ? prop : prop.split(".");
        objArray.sort(function(a,b){
            for (var p in propPath){
                if (a[propPath[p]] && b[propPath[p]]){
                    a = a[propPath[p]];
                    b = b[propPath[p]];
                }
            }
            // convert numeric strings to integers
            a = a.match(/^\d+$/) ? +a : a;
            b = b.match(/^\d+$/) ? +b : b;
            return ( (a < b) ? -1*direct : ((a > b) ? 1*direct : 0) );
        });
    }
}
//JSON Listing  
//var locationjson={"AK":[{"store":2784: Anchorage","2235: Fairbanks"],"AL":["2858: Birmingham","2310: Dothan","2175: Hoover","2287: Huntsville/Bridge Street TC","2139: Huntsville/Jones Valley Mall","2186: Spanish Fort","2295: Tuscaloosa"],"AR":["2721: Fayetteville","2250: Jonesboro","2658: Little Rock","2182: N. Little Rock","2134: Rogers"],"AZ":["2081: Chandler","2962: Flagstaff","2348: Gilbert","2147: Goodyear","2143: Mesa","2746: Peoria","2039: Phoenix/Desert Ridge","2211: Phoenix/Happy Valley","2560: Phoenix/Metro","2680: Scottsdale","2209: Surprise","2243: Tempe","2892: Tucson/Eastside","2804: Tucson/Westside","2756: Yuma"],"CA":["2882: Aliso Viejo","2173: Antioch","2851: Bakersfield","2715: Burbank","2956: Calabasas","2815: Chico","2300: Chino Hills","2284: Chula Vista","2885: Citrus Heights","2205: Corona","2274: Corte Madera","2774: Costa Mesa","2842: Dublin","2113: El Cerrito","2072: Emeryville","2785: Encinitas","2104: Escondido","2018: Fairfield","2112: Fresno","2095: Fullerton","2181: Gilroy","2303: Glendale","2296: Glendora","2743: Huntington Beach","2539: Irvine","2141: Irvine/Spectrum","2823: Irvine/Tustin","2733: La Mesa","2651: Long Beach","2911: Long Beach/Carson & 405","2089: Los Angeles","2986: Manhattan Beach","1847: Marina Del Rey","2164: Merced","2805: Modesto","2242: Montclair","2225: Newport Beach","2153: Oceanside","2954: Orange","2130: Palm Desert","2994: Palmdale","2729: Rancho Cucamonga","2871: Redding","2201: Redlands","1971: Redwood City","2083: Riverside","2031: Roseville","1996: Sacramento/Arden Fair","2269: Sacramento/Natomas","2208: San Bruno","1823: San Diego/Costa Verde","1822: San Diego/Loma Theatre","2984: San Diego/Mira Mesa","1978: San Diego/Mission Valley","1976: San Diego/Rancho Bernardo","2909: San Jose/Blossom Hill","2247: San Jose/Eastridge Mall","1944: San Jose/Stevens Creek Blvd","2676: San Luis Obispo","2936: San Mateo","2642: Santa Clarita","2575: Santa Monica","2603: Santa Rosa","2135: Santee","2920: Stockton","1837: Studio City","2991: Temecula","2751: Thousand Oaks","2111: Torrance","2054: Ventura","2090: Victorville","2569: Walnut Creek"],"CO":["2084: Aurora","2245: Aurora/Southlands Town Center","2333: Boulder","2863: Colorado Springs/Briargate","2608: Colorado Springs/Citadel","2854: Denver","2611: Ft. Collins","2554: Glendale","2852: Grand Junction","2877: Lakewood","2728: Littleton","2755: Lone Tree","2231: Loveland","2840: Pueblo","2091: Thornton","2718: Westminster"],"CT":["2223: Canton","2511: Danbury","2132: Enfield","1963: Farmington","2862: Glastonbury","2167: Manchester/Buckland Hills Mall","2240: Milford","1897: North Haven","2286: Stamford","2893: Waterbury","2279: West Hartford","2864: Westport"],"DC":["2040: Washington"],"DE":["2366: Newark","2949: Wilmington"],"FL":["2188: Altamonte Springs","2053: Boca Raton","2874: Boynton Beach","2595: Brandon","2859: Clearwater","2739: Coral Gables","2793: Coral Springs","2763: Daytona Beach","2051: Destin","2283: Estero","2047: Fort Lauderdale","2711: Ft. Myers","2683: Jacksonville/San Jose Blvd","2214: Jacksonville/St. Johns Town Ctr","2816: Jensen Beach","2844: Merritt Island","2792: Miami","2632: Naples","2320: Ocala","2998: Orlando","2704: Orlando/Colonial","2576: Orlando/Florida Mall","2120: Orlando/Plaza Venezia","2945: Oviedo","2230: Palm Beach Gardens","2315: Pembroke Pines","2814: Pembroke Pines","2926: Pensacola","2592: Plantation","2878: S. Miami","2737: Sarasota","1986: St Petersburg","2796: St. Augustine","2849: Tallahassee","2849: Tampa/Carrollwood","2550: Tampa/Dale Mabry","2264: The Villages","2100: Wellington","2349: Wesley Chapel","2834: West Melbourne"],"GA":["1955: Alpharetta","2974: Athens","1907: Atlanta/Buckhead","2794: Atlanta/Cumberland","2204: Atlanta/Edgewood Retail","2846: Atlanta/Perimeter","2359: Augusta/Augusta Mall","2972: Buford","2101: Columbus","2330: Cumming","2334: Macon","2656: Marietta/Town Center","2157: Marietta","2865: Morrow","2297: Newnan","2070: Norcross","2146: Rome","2015: Savannah","2256: Snellville"],"HI":["2249: Honolulu","2276: Lahaina"],"IA":["2587: Cedar Rapids","2917: Coralville","2970: Davenport","2179: Sioux City","2168: Waterloo","2921: West Des Moines","2220: West Des Moines/Jordan Creek"],"ID":["2740: Boise","2246: Idaho Falls","2663: Twin Falls"],"IL":["2872: Arlington Heights","2861: Bloomingdale","2590: Bloomington","2304: Bolingbrook","2769: Bourbonnais","2691: Carbondale","2714: Champaign","2780: Chicago/Clybourne","2622: Chicago/Old Orchard","2508: Chicago/Schaumburg","2552: Chicago/Skokie","2922: Chicago/State and Elm","2959: Crystal Lake","2032: Deer Park","2020:Deerfield","2236: Evanston","2258: Fairview Heights","2106: Geneva","2723: Joliet","2290: Lincolnshire","2904: Naperville","2361: Oak Brook","2964: Orland Park","2778: Peoria","2308: Rockford","2565: Springfield","2695: Vernon Hills","2185: West Dundee"],"IN":["2741: Bloomington","2933: Carmel","2692: Evansville","2036: Fort Wayne","2313: Fort Wayne/Glenbrook Square Mall","2329: Greenwood","2372: Indianapolis","2810: Lafayette","2358: Mishawaka","2149: Noblesville","2260: Plainfield","2138: Valparaiso"],"KS":["2668: Leawood","2352: Overland Park","2726: Topeka","2319: Wichita"],"KY":["2753: Bowling Green","2108: Elizabethtown","2654: Florence","2954: Lexington","2705: Louisville","2196: Louisville/The Summit","2059: Newport"],"LA":["2837: Baton Rouge","2263: Baton Rouge/Perkins Rowe","2071: Harvey","2730: Lafayette","2983: Mandeville","2768: Metairie","2856: Shreveport"],"MA":["2829: Bellingham","2115: Boston/Prudential Center","1989: Braintree","2966: Burlington","2935: East Walpole","2645: Framingham","2088: Hadley","2194: Hingham","2747: Holyoke","2092: Hyannis","2903: Leominster","2206: Millbury","1962: North Dartmouth","2993: Peabody","2661: Pittsfield","2798: Saugus","2172: Worcester"],"MD":["2866: Annapolis","2166: Baltimore/Pikesville","2881: Baltimore/Power Plant","2890: Baltimore/White Marsh","2817: Bel Air","2750: Bethesda","2093: Bowie","2831: Ellicott City","2257: Frederick","2971: Gaithersburg","2764: Rockville","2145: Salisbury","2940: Towson"],"ME":["2752: Augusta"],"MI":["2253: Allen Park","2107: Ann Arbor","2069: Battle Creek","2244: Flint","2696: Fort Gratiot","2356: Grand Rapids","2008: Grandville","2251: Green Oak Township","1892: Grosse Pointe","2042: Holland","2025: Lansing","2169: Midland","2808: Muskegon","2648: Northville","2588: Portage","2627: Rochester Hills","2841: Saginaw","2830: Shelby Township","2923: Troy","2629: West Bloomfield"],"MN":["2786: Blaine","2969: Bloomington","2096: Burnsville","2098: Duluth","2820: Eagan","2048: Eden Prairie","2514: Edina","2270: Mankato","2749: Maple Grove","2227: Maplewood","2516: Minneapolis/Calhoun","2564: Minneapolis/Downtown","2190: Minnetonka","2080: Rochester","2614: Roseville","2967: St. Cloud","2518: Woodbury"],"MO":["2272: Cape Girardeau","2192: Chesterfield","2074: Columbia","2350: Des Peres","2232: Fenton","2732: Independence","2161: Jefferson City","2574: Kansas City/Country Club Plaza","2184: Kansas City/Zona Rosa","2542: Ladue","2931: Springfield","2237: St Peters"],"MS":["2961: Gulfport","2318: Ridgeland","2346: Tupelo"],"MT":["2765: Billings","2117: Bozeman","2895: Great Falls","2779: Missoula"],"NC":["2347: Asheville/Asheville Mall","2326: Asheville/Town Square","2285: Burlington","2647: Cary","2317: Charlotte/Morrison Place","2812: Charlotte/The Arboretum","2631: Durham/New Hope Commons","2109: Durham/The Streets at Southpoint","2934: Fayetteville","2795: Greensboro","2775: Greenville","2947: Hickory","2806: High Point","2118: Huntersville","2307: Jacksonville","2254: Pineville","2171: Raleigh/Brier Creek Commons","1990: Raleigh/Crabtree Mall","2126: Raleigh/Triangle Town Center","2156: Wilmington","2761: Winston-Salem"],"ND":["2857: Bismarck","2606: Fargo","2291: Minot"],"NE":["2535: Lincoln/O Street","2939: Lincoln/Pine Lake Road","2836: Omaha/Crossroads","2662: Omaha/Oakview Mall"],"NH":["2052: Manchester","2791: Nashua","2988: Newington","2605: Salem"],"NJ":["2803: Brick","2140: Bridgewater","2288: Cherry Hill","2946: Clark","2932: Clifton","2891: Deptford","2924: East Brunswick","2363: Eatontown","2162: Edison","2609: Freehold","2228: Hackensack","2217: Hamilton","2030: Holmdel","2191: Howell","1980: Ledgewood","2340: Livingston","2664: Marlton","2797: Moorestown","2980: Morris Plains","2985: North Brunswick","2597: Paramus","2368: Princeton","1884: Springfield","1977: Woodland Park"],"NM":["2049: Albuquerque/Coronado Mall","2842: Albuquerque/West Side","2144: Las Cruces"],"NV":["2745: Henderson","2772: Las Vegas/Northwest","2128: Las Vegas/Summerlin","2938: Reno"],"NY":["2275: Albany","2958: Amherst","2963: Bay Shore","2562: Bayside","2979: Bronx","2965: Brooklyn/Court Street","2876: Brooklyn/Park Slope","2301: Buffalo/Clarence","2306: Buffalo/McKinley Mall","2216: Carle Place","2908: Dewitt","2327: East Northport","2825: Elmira","2713: Forest Hills","2122: Ithaca","2226: Kingston","2325: Lake Grove","2197: Liverpool","1912: Manhasset","2547: Massapequa Park","2897: Mohegan Lake","1960: Nanuet","2133: New Hartford","1072: New Hyde Park","2215: Newburgh","1979: NYC/82nd & Broadway","2278: NYC/86th & Lexington Ave.","2618: NYC/Citigroup","2234: NYC/New York/555 Fifth Ave","2255: NYC/Tribeca","2675: NYC/Union Square","2886: Poughkeepsie","2848: Rochester/Greece","2790: Rochester/Pittsford","2019: Saratoga Springs","2021: Staten Island","2981: Vestal","2229: Webster","2905: West Nyack","2202: White Plains","2889: Yonkers"],"OH":["2902: Akron","2873: Beavercreek","2809: Cincinnati","2925: Columbus/Easton","2860: Columbus/Lennox Town","2354: Columbus/Polaris Fashion Center","1968: Columbus/Sawmill","2527: Columbus/Upper Arlington","2777: Dayton","2731: Mansfield","2298: Maumee","2079: Mentor","2078: Pickerington","2591: Toledo","2170: West Chester","2155: Westlake","2154: Woodmere","2724: Youngstown"],"OK":["2807: Norman","2694: Oklahoma City/May Ave.","2725: Oklahoma City/Quail Springs","2845: Tulsa/Southroads","2585: Tulsa/Woodland Plaza"],"OR":["2748: Beaverton","2887: Bend","29789: Eugene","2771: Medford","2262: Portland/Clackamas Town Ctr Mal","2077: Portland/Lloyd Center","2371: Tigard"],"PA":["2198: Altoona","2351: Bensalem","1913: Broomall","2046: Camp Hill","2259: Center Valley","2819: Cranberry Township","2869: Devon","2210: Easton","2572: Erie","2086: Exton","2697: Fairless Hills","2880: Greensburg","2076: Homestead","2916: Lancaster","2203: Monroeville","2976: North Wales","2850: Philadelphia","2367: Pittsburgh/Settlers Ridge","2233: Pittsburgh/South Hills Village","2898: Pittsburgh/Waterworks","2992: Plymouth Meeting","2826: State College","2323: Whitehall","2996: Wilkes-Barre","2782: Willow Grove","2050: Wyomissing"],"RI":["2160: Middletown","2082: Smithfield","2159: Warwick"],"SC":["2915: Charleston/Northwoods","2919: Charleston/Westwood","2868: Columbia","2282: Florence","2558: Greenville","2221: Greenville/The Shops@Greenridge","2914: Hilton Head Island","2973: Mt. Pleasant","2289: Myrtle Beach","2758: Spartanburg"],"SD":["2968: Sioux Falls"],"TN":["2701: Brentwood","2337: Chattanooga","2224: Collierville","2312: Hendersonville","2129: Johnson City","2838: Knoxville","2822: Memphis","2292: Murfreesboro"],"TX":["2665: Amarillo","2342: Arlington/Relo","2536: Austin/Arboretum","2928: Austin/Sunset Valley","2943: Austin/The Homestead","2127: Beaumont","2267: Bee Cave","2305: Cedar Hill","2875: College Station","2818: Corpus Christi","2884: Dallas/North Park","2239: Dallas/Preston & Royal","2268: Dallas/Prestonwood Center","2193: Denton","2370: El Paso/ The Fountains at Farah","2634: El Paso/Sunland Park","2060: Frisco","22076: Ft Worth","2222: Garland","2324: Harker Heights","2271: Highland Village","2847: Houston Champions","2321: Houston/River Oaks Shopping Center","2183: Houston/The Centre in Copperfield","2643: Houston/Town & Country","2582: Houston/Vanderbilt Sq","2670: Houston/West Oaks Village","2635: Houston/Westheimer","2152: Humble","2344: Hurst","2657: Lewisville","2364: Lubbock","2119: McAllen/Northcross","2311: McAllen/Palms Crossing","2686: Midland","2066: Pasadena","2336: Pearland","2586: Plano","2566: Plano/Preston & Park","2009: Round Rock","2927: San Antonio 78232","2353: San Antonio 78256","2055: San Antonio/Bandera","2685: San Antonio/Ingram Festival","2802: San Antonio/San Pedro Crossing","2241: Southlake","2277: Sugar Land","2200: The Woodlands","2624: Tyler","2708: Waco","2671: Webster"],"UT":["2907: Layton","2639: Midvale","2148: Murray","2626: Orem","2087: Salt Lake City/Gateway","2941: Salt Lake City/Sugarhouse","2811: Sandy","2331: St. George","1946: West Bountiful","2137: West Jordan"],"VA":["2867: Alexandria","2068: Arlington/Clarendon Market","2559: Charlottesville","2735: Chesapeake","2067: Christiansburg","2937: Fairfax","2712: Falls Church","2369: Fredericksburg","2065: Glen Allen","2309: Hampton","2131: Harrisonburg","2026: Lynchburg","2948: Manassas","2238: McLean","2773: Newport News","2995: Richmond 23230","2214: Richmond 23235","2029: Richmond/Short Pump","2294: Roanoke 24012","2870: Roanoke/Tanglewood","2982: Springfield","2637: Virginia Beach 23462","2195: Virginia Beach/Lynnhaven Mall","2265: Williamsburg"],"VT":["2776: S. Burlington"],"WA":["1915: Bellevue","2530: Bellevue/Crossroads","2682: Bellingham","2706: Federal Way","2617: Issaquah","2879: Kennewick","2832: Lakewood","2888: Lynnwood","2653: Olympia","2957: Seattle/Downtown","2280: Seattle/Northgate","2218: Seattle/W. Seattle","2281: Silverdale","2951: Spokane/Eastside","2997: Spokane/Northtown Mall","2607: Tukwila","2679: Vancouver","2910: Woodinville"],"WI":["2213: Brookfield","2252: Glendale","2977: Grand Chute","2085: Green Bay","2594: Greenfield","2248: La Crosse","2720: Madison","2174: Madison/East Towne Mall","2037: Racine","2125: Wausau","2944: Wauwatosa"],"WV":["2189: Morgantown"],"WY":["2674: Cheyenne"]};

var locationjson = {
  "AK": [
    {
      "store": 2235,
      "name": "Fairbanks"
    },
    {
      "store": 2784,
      "name": "Anchorage"
    }
  ],
  "AL": [
    {
      "store": 2139,
      "name": "Jones Valley Mall"
    },
    {
      "store": 2175,
      "name": "Patton Creek"
    },
    {
      "store": 2186,
      "name": "Eastern Shore"
    },
    {
      "store": 2287,
      "name": "Huntsville"
    },
    {
      "store": 2295,
      "name": "Tuscaloosa"
    },
    {
      "store": 2310,
      "name": "Dothan"
    },
    {
      "store": 2858,
      "name": "The Summit Birmingham"
    }
  ],
  "AR": [
    {
      "store": 2134,
      "name": "Scottsdale Center"
    },
    {
      "store": 2182,
      "name": "N. Little Rock"
    },
    {
      "store": 2250,
      "name": "The Mall at Turtle Creek"
    },
    {
      "store": 2658,
      "name": "Little Rock"
    },
    {
      "store": 2721,
      "name": "Fayetteville"
    }
  ],
  "AZ": [
    {
      "store": 2039,
      "name": "Desert Ridge"
    },
    {
      "store": 2081,
      "name": "Chandler Fashion Mall"
    },
    {
      "store": 2143,
      "name": "Dana Park Village Square"
    },
    {
      "store": 2147,
      "name": "Palm Valley Pavillions West"
    },
    {
      "store": 2209,
      "name": "Surprise Marketplace"
    },
    {
      "store": 2211,
      "name": "Happy Valley"
    },
    {
      "store": 2243,
      "name": "Tempe Marketplace"
    },
    {
      "store": 2348,
      "name": "San Tan Village"
    },
    {
      "store": 2560,
      "name": "Metro"
    },
    {
      "store": 2680,
      "name": "Pima & Shea"
    },
    {
      "store": 2746,
      "name": "Arrowhead"
    },
    {
      "store": 2756,
      "name": "Yuma"
    },
    {
      "store": 2804,
      "name": "Westside"
    },
    {
      "store": 2892,
      "name": "Eastside"
    },
    {
      "store": 2962,
      "name": "Flagstaff"
    }
  ],
  "CA": [
    {
      "store": 1847,
      "name": "Marina Del Rey"
    },
    {
      "store": 1944,
      "name": "Stevens Creek Blvd"
    },
    {
      "store": 1971,
      "name": "Redwood City"
    },
    {
      "store": 1976,
      "name": "Rancho Bernardo"
    },
    {
      "store": 1978,
      "name": "Mission Valley"
    },
    {
      "store": 1996,
      "name": "Arden Fair"
    },
    {
      "store": 2018,
      "name": "Gateway Courtyard"
    },
    {
      "store": 2031,
      "name": "Roseville"
    },
    {
      "store": 2054,
      "name": "Ventura"
    },
    {
      "store": 2072,
      "name": "Bay Street - Emeryville"
    },
    {
      "store": 2083,
      "name": "Galleria at Tyler"
    },
    {
      "store": 2089,
      "name": "The Grove at Farmers Market"
    },
    {
      "store": 2090,
      "name": "Victorville Mall"
    },
    {
      "store": 2095,
      "name": "Fullerton"
    },
    {
      "store": 2104,
      "name": "Escondido"
    },
    {
      "store": 2111,
      "name": "Del Amo"
    },
    {
      "store": 2112,
      "name": "Fresno"
    },
    {
      "store": 2113,
      "name": "El Cerrito"
    },
    {
      "store": 2130,
      "name": "Palm Desert"
    },
    {
      "store": 2135,
      "name": "Trolley Square"
    },
    {
      "store": 2141,
      "name": "Spectrum"
    },
    {
      "store": 2153,
      "name": "Oceanside"
    },
    {
      "store": 2164,
      "name": "Merced"
    },
    {
      "store": 2173,
      "name": "Antioch"
    },
    {
      "store": 2181,
      "name": "Gilroy"
    },
    {
      "store": 2201,
      "name": "Redlands"
    },
    {
      "store": 2205,
      "name": "South Corona"
    },
    {
      "store": 2208,
      "name": "The Shops at Tanforan"
    },
    {
      "store": 2225,
      "name": "Fashion Island"
    },
    {
      "store": 2242,
      "name": "Montclair Place"
    },
    {
      "store": 2247,
      "name": "Eastridge Mall"
    },
    {
      "store": 2269,
      "name": "Natomas"
    },
    {
      "store": 2274,
      "name": "Corte Madera"
    },
    {
      "store": 2284,
      "name": "Eastlake"
    },
    {
      "store": 2296,
      "name": "Glendora"
    },
    {
      "store": 2300,
      "name": "Chino Hills"
    },
    {
      "store": 2303,
      "name": "Glendale Americana"
    },
    {
      "store": 2539,
      "name": "Irvine"
    },
    {
      "store": 2575,
      "name": "3rd Street Promenade"
    },
    {
      "store": 2603,
      "name": "Santa Rosa"
    },
    {
      "store": 2642,
      "name": "Valencia"
    },
    {
      "store": 2651,
      "name": "Long Beach"
    },
    {
      "store": 2676,
      "name": "San Luis Obispo"
    },
    {
      "store": 2715,
      "name": "Media City Center"
    },
    {
      "store": 2729,
      "name": "Rancho Cucamonga"
    },
    {
      "store": 2733,
      "name": "Grossmont Ctr"
    },
    {
      "store": 2743,
      "name": "Huntington Beach"
    },
    {
      "store": 2751,
      "name": "Thousand Oaks"
    },
    {
      "store": 2774,
      "name": "Costa Mesa"
    },
    {
      "store": 2785,
      "name": "Encinitas"
    },
    {
      "store": 2805,
      "name": "Modesto"
    },
    {
      "store": 2815,
      "name": "Chico"
    },
    {
      "store": 2823,
      "name": "Tustin"
    },
    {
      "store": 2851,
      "name": "Bakersfield"
    },
    {
      "store": 2871,
      "name": "Redding"
    },
    {
      "store": 2882,
      "name": "Aliso Viejo"
    },
    {
      "store": 2885,
      "name": "Birdcage"
    },
    {
      "store": 2909,
      "name": "Blossom Hill"
    },
    {
      "store": 2911,
      "name": "Carson & 605"
    },
    {
      "store": 2920,
      "name": "Weberstown"
    },
    {
      "store": 2936,
      "name": "Hillsdale"
    },
    {
      "store": 2942,
      "name": "Dublin"
    },
    {
      "store": 2954,
      "name": "Orange"
    },
    {
      "store": 2956,
      "name": "Calabasas"
    },
    {
      "store": 2984,
      "name": "Mira Mesa"
    },
    {
      "store": 2986,
      "name": "Manhattan Beach"
    },
    {
      "store": 2991,
      "name": "Temecula"
    },
    {
      "store": 2994,
      "name": "Palmdale"
    }
  ],
  "CO": [
    {
      "store": 2084,
      "name": "Aurora"
    },
    {
      "store": 2091,
      "name": "Thorncreek Shopping Ctr."
    },
    {
      "store": 2231,
      "name": "The Promenade Shops at Centerra"
    },
    {
      "store": 2245,
      "name": "Southlands Town Center"
    },
    {
      "store": 2333,
      "name": "Crossroads Commons"
    },
    {
      "store": 2554,
      "name": "Co. Blvd II"
    },
    {
      "store": 2608,
      "name": "Citadel"
    },
    {
      "store": 2611,
      "name": "College Ave."
    },
    {
      "store": 2718,
      "name": "Westminster"
    },
    {
      "store": 2728,
      "name": "Denver"
    },
    {
      "store": 2755,
      "name": "Lone Tree"
    },
    {
      "store": 2840,
      "name": "Pueblo"
    },
    {
      "store": 2852,
      "name": "Grand Junction"
    },
    {
      "store": 2863,
      "name": "Briargate"
    },
    {
      "store": 2877,
      "name": "West Village"
    }
  ],
  "CT": [
    {
      "store": 1897,
      "name": "North Haven"
    },
    {
      "store": 1963,
      "name": "Farmington"
    },
    {
      "store": 2132,
      "name": "Enfield"
    },
    {
      "store": 2167,
      "name": "Buckland Hills Mall"
    },
    {
      "store": 2223,
      "name": "Canton"
    },
    {
      "store": 2240,
      "name": "Milford"
    },
    {
      "store": 2279,
      "name": "West Hartford"
    },
    {
      "store": 2286,
      "name": "Stamford Town Center"
    },
    {
      "store": 2511,
      "name": "Danbury Square"
    },
    {
      "store": 2862,
      "name": "Somerset Square"
    },
    {
      "store": 2864,
      "name": "Westport"
    },
    {
      "store": 2893,
      "name": "Waterbury"
    }
  ],
  "DE": [
    {
      "store": 2366,
      "name": "Christiana Mall"
    },
    {
      "store": 2949,
      "name": "Concord Pike"
    }
  ],
  "FL": [
    {
      "store": 1986,
      "name": "St. Petersburg-Tyrone"
    },
    {
      "store": 2047,
      "name": "Fort Lauderdale"
    },
    {
      "store": 2051,
      "name": "Crystal Beach Plaza"
    },
    {
      "store": 2053,
      "name": "Boca Raton"
    },
    {
      "store": 2100,
      "name": "Wellington"
    },
    {
      "store": 2120,
      "name": "Plaza Venezia"
    },
    {
      "store": 2188,
      "name": "Altamonte Mall"
    },
    {
      "store": 2214,
      "name": "St Johns Town Center"
    },
    {
      "store": 2230,
      "name": "Palm Beach Gardens"
    },
    {
      "store": 2264,
      "name": "Lake Sumter Market Square"
    },
    {
      "store": 2283,
      "name": "Bonita Springs"
    },
    {
      "store": 2315,
      "name": "Pembroke Pines"
    },
    {
      "store": 2320,
      "name": "Market Street at Heath Brook"
    },
    {
      "store": 2349,
      "name": "Shops at Wiregrass"
    },
    {
      "store": 2550,
      "name": "Dale Mabry"
    },
    {
      "store": 2576,
      "name": "Florida Mall"
    },
    {
      "store": 2592,
      "name": "Broward Mall"
    },
    {
      "store": 2595,
      "name": "Brandon Square"
    },
    {
      "store": 2632,
      "name": "Waterside Shops"
    },
    {
      "store": 2683,
      "name": "San Jose Blvd."
    },
    {
      "store": 2704,
      "name": "Colonial"
    },
    {
      "store": 2711,
      "name": "Marketplace SC"
    },
    {
      "store": 2737,
      "name": "Sarasota II"
    },
    {
      "store": 2739,
      "name": "Coral Gables"
    },
    {
      "store": 2763,
      "name": "Daytona Beach"
    },
    {
      "store": 2767,
      "name": "Carrollwood"
    },
    {
      "store": 2792,
      "name": "West Kendall"
    },
    {
      "store": 2793,
      "name": "Coral Springs"
    },
    {
      "store": 2796,
      "name": "St. Augustine"
    },
    {
      "store": 2814,
      "name": "Pembroke Pines"
    },
    {
      "store": 2816,
      "name": "Jensen Beach"
    },
    {
      "store": 2834,
      "name": "Melbourne"
    },
    {
      "store": 2844,
      "name": "Merritt Island"
    },
    {
      "store": 2849,
      "name": "Tallahassee Mall"
    },
    {
      "store": 2859,
      "name": "Clearwater"
    },
    {
      "store": 2874,
      "name": "Boynton Beach"
    },
    {
      "store": 2878,
      "name": "Sunset"
    },
    {
      "store": 2926,
      "name": "Pensacola"
    },
    {
      "store": 2945,
      "name": "Oviedo Mall"
    },
    {
      "store": 2998,
      "name": "Waterford Lakes"
    }
  ],
  "GA": [
    {
      "store": 1907,
      "name": "Buckhead"
    },
    {
      "store": 1955,
      "name": "Mansell Crossings SC"
    },
    {
      "store": 2015,
      "name": "Oglethorpe Mall"
    },
    {
      "store": 2070,
      "name": "The Forum"
    },
    {
      "store": 2101,
      "name": "Columbus"
    },
    {
      "store": 2146,
      "name": "Riverbend Market Place"
    },
    {
      "store": 2157,
      "name": "The Ave at West Cobb"
    },
    {
      "store": 2204,
      "name": "Edgewood Retail"
    },
    {
      "store": 2256,
      "name": "The Shoppes at Webb Gin"
    },
    {
      "store": 2297,
      "name": "Newnan"
    },
    {
      "store": 2330,
      "name": "The Collection at Forsyth"
    },
    {
      "store": 2334,
      "name": "The Shoppes at River Crossing"
    },
    {
      "store": 2359,
      "name": "Augusta Mall"
    },
    {
      "store": 2656,
      "name": "Town Center Prado"
    },
    {
      "store": 2794,
      "name": "Cumberland"
    },
    {
      "store": 2846,
      "name": "Perimeter"
    },
    {
      "store": 2865,
      "name": "Southlake"
    },
    {
      "store": 2972,
      "name": "Mall Of Georgia"
    },
    {
      "store": 2974,
      "name": "Athens"
    }
  ],
  "HI": [
    {
      "store": 2249,
      "name": "Ala Moana Mall"
    },
    {
      "store": 2276,
      "name": "Maui"
    }
  ],
  "IA": [
    {
      "store": 2168,
      "name": "Waterloo"
    },
    {
      "store": 2179,
      "name": "Southern Hills Mall"
    },
    {
      "store": 2220,
      "name": "Jordan Creek"
    },
    {
      "store": 2587,
      "name": "Cedar Rapids"
    },
    {
      "store": 2917,
      "name": "Mall Site"
    },
    {
      "store": 2921,
      "name": "Des Moines"
    },
    {
      "store": 2970,
      "name": "North Park Mall"
    }
  ],
  "ID": [
    {
      "store": 2246,
      "name": "Grand Teton Mall"
    },
    {
      "store": 2663,
      "name": "Twin Falls"
    },
    {
      "store": 2740,
      "name": "Boise II"
    }
  ],
  "IL": [
    {
      "store": 2020,
      "name": "Deerfield Square"
    },
    {
      "store": 2032,
      "name": "Deer Park"
    },
    {
      "store": 2106,
      "name": "Geneva Commons"
    },
    {
      "store": 2185,
      "name": "Spring Hill Mall"
    },
    {
      "store": 2236,
      "name": "Sherman Plaza"
    },
    {
      "store": 2258,
      "name": "Fairview Heights"
    },
    {
      "store": 2304,
      "name": "Bolingbrook"
    },
    {
      "store": 2308,
      "name": "Cherryvale Mall"
    },
    {
      "store": 2361,
      "name": "Oakbrook Center"
    },
    {
      "store": 2508,
      "name": "Schaumburg"
    },
    {
      "store": 2552,
      "name": "Skokie Village Crossing"
    },
    {
      "store": 2565,
      "name": "Springfield"
    },
    {
      "store": 2590,
      "name": "Bloomington"
    },
    {
      "store": 2622,
      "name": "Skokie Old Orchard"
    },
    {
      "store": 2691,
      "name": "Carbondale"
    },
    {
      "store": 2695,
      "name": "Vernon Hills"
    },
    {
      "store": 2714,
      "name": "Champaign"
    },
    {
      "store": 2723,
      "name": "Joliet"
    },
    {
      "store": 2769,
      "name": "Water Tower Plaza"
    },
    {
      "store": 2778,
      "name": "Peoria"
    },
    {
      "store": 2780,
      "name": "Clybourn"
    },
    {
      "store": 2861,
      "name": "Bloomingdale"
    },
    {
      "store": 2904,
      "name": "Naperville"
    },
    {
      "store": 2922,
      "name": "State and Elm"
    },
    {
      "store": 2959,
      "name": "Crystal Lake"
    },
    {
      "store": 2964,
      "name": "Orland Park Place"
    }
  ],
  "IN": [
    {
      "store": 2036,
      "name": "Ft. Wayne"
    },
    {
      "store": 2138,
      "name": "Valparaiso"
    },
    {
      "store": 2149,
      "name": "Stony Creek Marketplace"
    },
    {
      "store": 2260,
      "name": "Shops at Perry Crossing"
    },
    {
      "store": 2313,
      "name": "Glenbrook Square Mall"
    },
    {
      "store": 2329,
      "name": "Greenwood Park"
    },
    {
      "store": 2358,
      "name": "University Park Mall"
    },
    {
      "store": 2372,
      "name": "The Shops at River Crossing"
    },
    {
      "store": 2692,
      "name": "Evansville"
    },
    {
      "store": 2741,
      "name": "Bloomington"
    },
    {
      "store": 2810,
      "name": "Lafayette"
    },
    {
      "store": 2933,
      "name": "Carmel"
    }
  ],
  "KS": [
    {
      "store": 2319,
      "name": "Bradley Fair"
    },
    {
      "store": 2352,
      "name": "Oak Park Mall"
    },
    {
      "store": 2668,
      "name": "Town Center Plaza"
    },
    {
      "store": 2726,
      "name": "Topeka"
    }
  ],
  "KY": [
    {
      "store": 2059,
      "name": "Newport on the Levee"
    },
    {
      "store": 2108,
      "name": "Elizabethtown Mall"
    },
    {
      "store": 2196,
      "name": "Paddock Shops"
    },
    {
      "store": 2654,
      "name": "Florence"
    },
    {
      "store": 2705,
      "name": "The Shoppes at Plainview"
    },
    {
      "store": 2753,
      "name": "Campbell Lane"
    },
    {
      "store": 2953,
      "name": "Man-O-War"
    }
  ],
  "LA": [
    {
      "store": 2071,
      "name": "Westbank"
    },
    {
      "store": 2263,
      "name": "Perkins Rowe"
    },
    {
      "store": 2730,
      "name": "Lafayette"
    },
    {
      "store": 2768,
      "name": "Metairie"
    },
    {
      "store": 2837,
      "name": "Baton Rouge"
    },
    {
      "store": 2856,
      "name": "Youree Dr."
    },
    {
      "store": 2983,
      "name": "Premier Center II"
    }
  ],
  "MA": [
    {
      "store": 1962,
      "name": "North Dartmouth"
    },
    {
      "store": 1989,
      "name": "Braintree"
    },
    {
      "store": 2088,
      "name": "Hadley"
    },
    {
      "store": 2092,
      "name": "Cape Cod Mall"
    },
    {
      "store": 2115,
      "name": "Prudential Center"
    },
    {
      "store": 2172,
      "name": "Worcester"
    },
    {
      "store": 2194,
      "name": "Hingham"
    },
    {
      "store": 2206,
      "name": "Millbury"
    },
    {
      "store": 2645,
      "name": "Framingham"
    },
    {
      "store": 2661,
      "name": "Pittsfield"
    },
    {
      "store": 2747,
      "name": "Holyoke"
    },
    {
      "store": 2798,
      "name": "Saugus"
    },
    {
      "store": 2829,
      "name": "Bellingham"
    },
    {
      "store": 2903,
      "name": "Leominster"
    },
    {
      "store": 2935,
      "name": "Walpole Mall"
    },
    {
      "store": 2966,
      "name": "Burlington"
    },
    {
      "store": 2993,
      "name": "Peabody"
    }
  ],
  "MD": [
    {
      "store": 2093,
      "name": "Bowie"
    },
    {
      "store": 2145,
      "name": "Salisbury"
    },
    {
      "store": 2166,
      "name": "Pikesville"
    },
    {
      "store": 2257,
      "name": "Francis Scott Key Mall"
    },
    {
      "store": 2750,
      "name": "Bethesda"
    },
    {
      "store": 2764,
      "name": "Rockville Pike"
    },
    {
      "store": 2817,
      "name": "Bel Air"
    },
    {
      "store": 2831,
      "name": "Ellicott City"
    },
    {
      "store": 2866,
      "name": "Annapolis"
    },
    {
      "store": 2881,
      "name": "Power Plant"
    },
    {
      "store": 2890,
      "name": "White Marsh"
    },
    {
      "store": 2940,
      "name": "Towson"
    },
    {
      "store": 2971,
      "name": "Gaithersburg"
    }
  ],
  "ME": [
    {
      "store": 2742,
      "name": "Augusta"
    }
  ],
  "MI": [
    {
      "store": 1892,
      "name": "Grosse Pointe"
    },
    {
      "store": 2008,
      "name": "Rivertown Crossing"
    },
    {
      "store": 2025,
      "name": "Lansing Mall"
    },
    {
      "store": 2042,
      "name": "Felch St Plaza"
    },
    {
      "store": 2069,
      "name": "Lakeview Sq."
    },
    {
      "store": 2107,
      "name": "Ann Arbor"
    },
    {
      "store": 2169,
      "name": "Midland Mall"
    },
    {
      "store": 2244,
      "name": "Genesee Valley Mall"
    },
    {
      "store": 2251,
      "name": "Green Oak Village Place"
    },
    {
      "store": 2253,
      "name": "Fairlane Green"
    },
    {
      "store": 2356,
      "name": "Woodland Mall"
    },
    {
      "store": 2588,
      "name": "Kalamazoo"
    },
    {
      "store": 2627,
      "name": "Rochester Hills"
    },
    {
      "store": 2648,
      "name": "Livonia"
    },
    {
      "store": 2696,
      "name": "Port Huron"
    },
    {
      "store": 2808,
      "name": "Muskegon"
    },
    {
      "store": 2830,
      "name": "Shelby Township"
    },
    {
      "store": 2841,
      "name": "Saginaw"
    },
    {
      "store": 2923,
      "name": "Troy"
    }
  ],
  "MN": [
    {
      "store": 2048,
      "name": "Eden Prairie Center"
    },
    {
      "store": 2080,
      "name": "Apache Mall"
    },
    {
      "store": 2096,
      "name": "Burnsville"
    },
    {
      "store": 2098,
      "name": "Miller Hill Mall"
    },
    {
      "store": 2190,
      "name": "Ridgehaven Mall"
    },
    {
      "store": 2227,
      "name": "Maplewood Mall"
    },
    {
      "store": 2270,
      "name": "Mankato"
    },
    {
      "store": 2514,
      "name": "Galleria"
    },
    {
      "store": 2516,
      "name": "Calhoun"
    },
    {
      "store": 2518,
      "name": "Woodbury"
    },
    {
      "store": 2564,
      "name": "Downtown"
    },
    {
      "store": 2614,
      "name": "Roseville II"
    },
    {
      "store": 2749,
      "name": "Maple Grove"
    },
    {
      "store": 2786,
      "name": "Northtown"
    },
    {
      "store": 2820,
      "name": "Eagan"
    },
    {
      "store": 2967,
      "name": "St. Cloud"
    },
    {
      "store": 2969,
      "name": "Mall of America"
    }
  ],
  "MO": [
    {
      "store": 2074,
      "name": "Columbia Mall"
    },
    {
      "store": 2161,
      "name": "Wildwood Crossing"
    },
    {
      "store": 2184,
      "name": "Zona Rosa"
    },
    {
      "store": 2192,
      "name": "Chesterfield Oaks"
    },
    {
      "store": 2232,
      "name": "Fenton Commons Shopping Center"
    },
    {
      "store": 2237,
      "name": "Mid Rivers"
    },
    {
      "store": 2272,
      "name": "Cape Girardeau"
    },
    {
      "store": 2350,
      "name": "West County Mall"
    },
    {
      "store": 2542,
      "name": "Ladue Rd"
    },
    {
      "store": 2574,
      "name": "Country Club Plaza"
    },
    {
      "store": 2732,
      "name": "Independence"
    },
    {
      "store": 2931,
      "name": "Springfield"
    }
  ],
  "MS": [
    {
      "store": 2318,
      "name": "Colony Park"
    },
    {
      "store": 2346,
      "name": "The Mall at Barnes Crossing"
    },
    {
      "store": 2961,
      "name": "Gulfport"
    }
  ],
  "MT": [
    {
      "store": 2117,
      "name": "Gallatin Valley Mall"
    },
    {
      "store": 2765,
      "name": "Billings"
    },
    {
      "store": 2779,
      "name": "Missoula"
    },
    {
      "store": 2895,
      "name": "Great Falls"
    }
  ],
  "NC": [
    {
      "store": 1990,
      "name": "Crabtree Mall"
    },
    {
      "store": 2109,
      "name": "The Streets at Southpoint"
    },
    {
      "store": 2118,
      "name": "Birkdale"
    },
    {
      "store": 2126,
      "name": "Triangle Town Center"
    },
    {
      "store": 2156,
      "name": "Mayfaire Town Ctr"
    },
    {
      "store": 2171,
      "name": "Brier Creek Commons"
    },
    {
      "store": 2254,
      "name": "Carolina Place Mall"
    },
    {
      "store": 2285,
      "name": "Burlington"
    },
    {
      "store": 2307,
      "name": "Jacksonville Mall"
    },
    {
      "store": 2317,
      "name": "Morrison"
    },
    {
      "store": 2326,
      "name": "Town Square at Biltmore Park"
    },
    {
      "store": 2347,
      "name": "Asheville Mall"
    },
    {
      "store": 2631,
      "name": "New Hope Commons"
    },
    {
      "store": 2647,
      "name": "Cary"
    },
    {
      "store": 2761,
      "name": "Winston Salem"
    },
    {
      "store": 2775,
      "name": "Greenville"
    },
    {
      "store": 2795,
      "name": "Greensboro"
    },
    {
      "store": 2806,
      "name": "High Point"
    },
    {
      "store": 2812,
      "name": "The Arboretum"
    },
    {
      "store": 2934,
      "name": "Fayetteville"
    },
    {
      "store": 2947,
      "name": "Hickory"
    }
  ],
  "ND": [
    {
      "store": 2291,
      "name": "Dakota Square Mall"
    },
    {
      "store": 2606,
      "name": "Fargo"
    },
    {
      "store": 2857,
      "name": "Southridge Centre"
    }
  ],
  "NE": [
    {
      "store": 2535,
      "name": "Lincoln"
    },
    {
      "store": 2662,
      "name": "Oakview Mall"
    },
    {
      "store": 2836,
      "name": "Crossroads"
    },
    {
      "store": 2939,
      "name": "Lincoln"
    }
  ],
  "NH": [
    {
      "store": 2052,
      "name": "Manchester"
    },
    {
      "store": 2605,
      "name": "Salem"
    },
    {
      "store": 2791,
      "name": "Nashua"
    },
    {
      "store": 2988,
      "name": "Portsmouth"
    }
  ],
  "NJ": [
    {
      "store": 1884,
      "name": "Springfield"
    },
    {
      "store": 1977,
      "name": "Woodland Park"
    },
    {
      "store": 1980,
      "name": "Ledgewood"
    },
    {
      "store": 2030,
      "name": "Holmdel"
    },
    {
      "store": 2140,
      "name": "Somerville"
    },
    {
      "store": 2162,
      "name": "Menlo Park Mall"
    },
    {
      "store": 2191,
      "name": "Howell"
    },
    {
      "store": 2217,
      "name": "Hamilton"
    },
    {
      "store": 2228,
      "name": "The Shops @ Riverside"
    },
    {
      "store": 2288,
      "name": "Cherry Hill"
    },
    {
      "store": 2340,
      "name": "Livingston"
    },
    {
      "store": 2363,
      "name": "Monmouth Mall"
    },
    {
      "store": 2368,
      "name": "Market Fair"
    },
    {
      "store": 2597,
      "name": "Paramus"
    },
    {
      "store": 2609,
      "name": "Freehold"
    },
    {
      "store": 2664,
      "name": "Marlton"
    },
    {
      "store": 2797,
      "name": "Moorestown"
    },
    {
      "store": 2803,
      "name": "Brick"
    },
    {
      "store": 2891,
      "name": "Deptford"
    },
    {
      "store": 2924,
      "name": "East Brunswick"
    },
    {
      "store": 2932,
      "name": "Clifton"
    },
    {
      "store": 2946,
      "name": "Clark"
    },
    {
      "store": 2980,
      "name": "Morris Plains"
    },
    {
      "store": 2985,
      "name": "North Brunswick"
    }
  ],
  "NM": [
    {
      "store": 2049,
      "name": "Coronado Mall"
    },
    {
      "store": 2144,
      "name": "Mesilla Valley Mall"
    },
    {
      "store": 2842,
      "name": "West Side"
    }
  ],
  "NV": [
    {
      "store": 2128,
      "name": "Summerlin"
    },
    {
      "store": 2745,
      "name": "Henderson"
    },
    {
      "store": 2772,
      "name": "Northwest"
    },
    {
      "store": 2938,
      "name": "Reno"
    }
  ],
  "NY": [
    {
      "store": 1912,
      "name": "Manhasset"
    },
    {
      "store": 1960,
      "name": "Nanuet"
    },
    {
      "store": 1979,
      "name": "82nd & Broadway"
    },
    {
      "store": 2019,
      "name": "Saratoga"
    },
    {
      "store": 2021,
      "name": "Staten Island"
    },
    {
      "store": 2122,
      "name": "Ithaca"
    },
    {
      "store": 2133,
      "name": "New Hartford"
    },
    {
      "store": 2197,
      "name": "Clay"
    },
    {
      "store": 2202,
      "name": "City Center"
    },
    {
      "store": 2215,
      "name": "Newburgh Crossing"
    },
    {
      "store": 2216,
      "name": "Country Glen Center"
    },
    {
      "store": 2226,
      "name": "Kingston"
    },
    {
      "store": 2229,
      "name": "Towne Center at Webster"
    },
    {
      "store": 2234,
      "name": "Fifth Ave"
    },
    {
      "store": 2255,
      "name": "Tribeca"
    },
    {
      "store": 2275,
      "name": "Colonie Centre"
    },
    {
      "store": 2278,
      "name": "86th & Lexington"
    },
    {
      "store": 2301,
      "name": "Clarence Mall"
    },
    {
      "store": 2306,
      "name": "McKinley Mall"
    },
    {
      "store": 2325,
      "name": "Smith Haven Mall"
    },
    {
      "store": 2327,
      "name": "East Northport"
    },
    {
      "store": 2547,
      "name": "Massapequa"
    },
    {
      "store": 2618,
      "name": "Citigroup Center"
    },
    {
      "store": 2675,
      "name": "Union Square"
    },
    {
      "store": 2790,
      "name": "Pittsford"
    },
    {
      "store": 2825,
      "name": "Elmira"
    },
    {
      "store": 2848,
      "name": "Greece"
    },
    {
      "store": 2876,
      "name": "Park Slope"
    },
    {
      "store": 2886,
      "name": "Poughkeepsie"
    },
    {
      "store": 2889,
      "name": "Yonkers"
    },
    {
      "store": 2897,
      "name": "Mohegan Lake"
    },
    {
      "store": 2905,
      "name": "Palisades"
    },
    {
      "store": 2908,
      "name": "Dewitt"
    },
    {
      "store": 2958,
      "name": "Amherst"
    },
    {
      "store": 2963,
      "name": "Bay Shore"
    },
    {
      "store": 2965,
      "name": "Court Street"
    },
    {
      "store": 2979,
      "name": "Bay Plaza"
    },
    {
      "store": 2981,
      "name": "Vestal"
    }
  ],
  "OH": [
    {
      "store": 1968,
      "name": "Sawmill"
    },
    {
      "store": 2078,
      "name": "Pickerington"
    },
    {
      "store": 2079,
      "name": "Great Lakes Mall"
    },
    {
      "store": 2154,
      "name": "Eton Chagrin Boulevard"
    },
    {
      "store": 2155,
      "name": "Crocker Park"
    },
    {
      "store": 2170,
      "name": "Streets of Westchester"
    },
    {
      "store": 2298,
      "name": "The Shops at Fallen Timbers"
    },
    {
      "store": 2354,
      "name": "Polaris Fashion Center"
    },
    {
      "store": 2527,
      "name": "Upper Arlington"
    },
    {
      "store": 2591,
      "name": "Franklin Park"
    },
    {
      "store": 2724,
      "name": "Youngstown"
    },
    {
      "store": 2731,
      "name": "Mansfield"
    },
    {
      "store": 2777,
      "name": "Dayton Mall"
    },
    {
      "store": 2809,
      "name": "Fields Ertel"
    },
    {
      "store": 2860,
      "name": "Lennox Town"
    },
    {
      "store": 2873,
      "name": "Dayton @ Beavercreek"
    },
    {
      "store": 2902,
      "name": "Akron"
    },
    {
      "store": 2925,
      "name": "Easton"
    }
  ],
  "OK": [
    {
      "store": 2585,
      "name": "Woodland Plaza"
    },
    {
      "store": 2694,
      "name": "May Ave."
    },
    {
      "store": 2725,
      "name": "Quail Springs"
    },
    {
      "store": 2807,
      "name": "Norman"
    },
    {
      "store": 2845,
      "name": "Southroads"
    }
  ],
  "OR": [
    {
      "store": 2077,
      "name": "Lloyd Center"
    },
    {
      "store": 2262,
      "name": "Clackamas Town Ctr Mall"
    },
    {
      "store": 2371,
      "name": "Bridgeport"
    },
    {
      "store": 2748,
      "name": "Tanasbourne"
    },
    {
      "store": 2771,
      "name": "Medford"
    },
    {
      "store": 2887,
      "name": "Bend"
    },
    {
      "store": 2978,
      "name": "Eugene"
    }
  ],
  "PA": [
    {
      "store": 1913,
      "name": "Broomall"
    },
    {
      "store": 2046,
      "name": "Camp Hill Shopping Center"
    },
    {
      "store": 2050,
      "name": "Reading"
    },
    {
      "store": 2076,
      "name": "Waterfront/Homestead"
    },
    {
      "store": 2086,
      "name": "Exton"
    },
    {
      "store": 2198,
      "name": "Altoona"
    },
    {
      "store": 2203,
      "name": "Monroeville Mall"
    },
    {
      "store": 2210,
      "name": "Bethlehem"
    },
    {
      "store": 2233,
      "name": "South Hills Village"
    },
    {
      "store": 2259,
      "name": "Center Valley"
    },
    {
      "store": 2323,
      "name": "Lehigh Valley Mall"
    },
    {
      "store": 2351,
      "name": "Neshaminy Mall"
    },
    {
      "store": 2367,
      "name": "Settlers Ridge"
    },
    {
      "store": 2572,
      "name": "Erie"
    },
    {
      "store": 2697,
      "name": "The Court @ Oxford Valley"
    },
    {
      "store": 2782,
      "name": "Willow Grove"
    },
    {
      "store": 2819,
      "name": "Cranberry"
    },
    {
      "store": 2826,
      "name": "State College"
    },
    {
      "store": 2850,
      "name": "Downtown"
    },
    {
      "store": 2869,
      "name": "Valley Forge"
    },
    {
      "store": 2880,
      "name": "Greensburg"
    },
    {
      "store": 2898,
      "name": "Waterworks"
    },
    {
      "store": 2916,
      "name": "Lancaster"
    },
    {
      "store": 2976,
      "name": "Montgomeryville"
    },
    {
      "store": 2992,
      "name": "Plymouth Meeting"
    },
    {
      "store": 2996,
      "name": "Wilkes-Barre"
    }
  ],
  "RI": [
    {
      "store": 2082,
      "name": "Smithfield"
    },
    {
      "store": 2159,
      "name": "Warwick"
    },
    {
      "store": 2160,
      "name": "Middletown"
    }
  ],
  "SC": [
    {
      "store": 2221,
      "name": "The Shops at Greenridge"
    },
    {
      "store": 2282,
      "name": "Magnolia Mall"
    },
    {
      "store": 2289,
      "name": "The Market Common"
    },
    {
      "store": 2558,
      "name": "Greenville"
    },
    {
      "store": 2758,
      "name": "Spartanburg"
    },
    {
      "store": 2868,
      "name": "Midtown at Forest Acres"
    },
    {
      "store": 2914,
      "name": "Hilton Head"
    },
    {
      "store": 2915,
      "name": "Northwoods"
    },
    {
      "store": 2919,
      "name": "Westwood"
    },
    {
      "store": 2973,
      "name": "Towne Centre"
    }
  ],
  "SD": [
    {
      "store": 2968,
      "name": "Sioux Falls"
    }
  ],
  "TN": [
    {
      "store": 2129,
      "name": "Johnson City"
    },
    {
      "store": 2224,
      "name": "Carriage Crossing"
    },
    {
      "store": 2292,
      "name": "The Avenue at Murfreesboro"
    },
    {
      "store": 2312,
      "name": "The Streets of Indian Lake"
    },
    {
      "store": 2337,
      "name": "Hamilton Place"
    },
    {
      "store": 2701,
      "name": "Cool Springs"
    },
    {
      "store": 2822,
      "name": "Wolf Chase Galleria"
    },
    {
      "store": 2838,
      "name": "Suburban"
    }
  ],
  "TX": [
    {
      "store": 2009,
      "name": "Round Rock"
    },
    {
      "store": 2055,
      "name": "Bandera"
    },
    {
      "store": 2060,
      "name": "Stonebriar Mall"
    },
    {
      "store": 2066,
      "name": "Pasadena"
    },
    {
      "store": 2119,
      "name": "Northcross"
    },
    {
      "store": 2127,
      "name": "Parkdale Mall"
    },
    {
      "store": 2152,
      "name": "Deerbrook Mall"
    },
    {
      "store": 2183,
      "name": "The Centre in Copperfield"
    },
    {
      "store": 2193,
      "name": "Golden Triangle Mall"
    },
    {
      "store": 2200,
      "name": "The Woodlands"
    },
    {
      "store": 2207,
      "name": "Hulen Center"
    },
    {
      "store": 2222,
      "name": "Firewheel Mall"
    },
    {
      "store": 2239,
      "name": "Preston Royal Shopping Center"
    },
    {
      "store": 2241,
      "name": "Southlake Town Square"
    },
    {
      "store": 2267,
      "name": "Hill Country Galleria"
    },
    {
      "store": 2268,
      "name": "Prestonwood Center"
    },
    {
      "store": 2271,
      "name": "Highland Village"
    },
    {
      "store": 2277,
      "name": "First Colony Mall"
    },
    {
      "store": 2305,
      "name": "Hillside Village"
    },
    {
      "store": 2311,
      "name": "Palms Crossing"
    },
    {
      "store": 2321,
      "name": "River Oaks Shopping Center"
    },
    {
      "store": 2324,
      "name": "Market Heights"
    },
    {
      "store": 2336,
      "name": "Pearland"
    },
    {
      "store": 2342,
      "name": "The Parks at Arlington Mall"
    },
    {
      "store": 2344,
      "name": "Shops at North East Mall"
    },
    {
      "store": 2353,
      "name": "The Shops at La Cantera"
    },
    {
      "store": 2364,
      "name": "Lubbock"
    },
    {
      "store": 2370,
      "name": "Fountains at Farah"
    },
    {
      "store": 2536,
      "name": "Arboretum"
    },
    {
      "store": 2566,
      "name": "Preston & Park"
    },
    {
      "store": 2582,
      "name": "Vanderbilt Sq"
    },
    {
      "store": 2586,
      "name": "Creekwalk Village"
    },
    {
      "store": 2624,
      "name": "Tyler Broadway Pavilion"
    },
    {
      "store": 2634,
      "name": "Sunland Park"
    },
    {
      "store": 2635,
      "name": "Westheimer"
    },
    {
      "store": 2643,
      "name": "Town & Country"
    },
    {
      "store": 2657,
      "name": "Lewisville"
    },
    {
      "store": 2665,
      "name": "Amarillo"
    },
    {
      "store": 2670,
      "name": "West Oaks Village"
    },
    {
      "store": 2671,
      "name": "Baybrook"
    },
    {
      "store": 2685,
      "name": "Ingram Festival"
    },
    {
      "store": 2686,
      "name": "Midland"
    },
    {
      "store": 2708,
      "name": "Waco"
    },
    {
      "store": 2802,
      "name": "San Pedro"
    },
    {
      "store": 2818,
      "name": "Corpus Christi"
    },
    {
      "store": 2847,
      "name": "Houston Champions"
    },
    {
      "store": 2875,
      "name": "College Station"
    },
    {
      "store": 2884,
      "name": "Lincoln Park"
    },
    {
      "store": 2927,
      "name": "San Antonio"
    },
    {
      "store": 2928,
      "name": "Sunset Valley"
    },
    {
      "store": 2943,
      "name": "The Homestead"
    }
  ],
  "UT": [
    {
      "store": 1946,
      "name": "Gateway Crossing"
    },
    {
      "store": 2087,
      "name": "Gateway"
    },
    {
      "store": 2137,
      "name": "West Jordan"
    },
    {
      "store": 2148,
      "name": "The Pointe at 5300"
    },
    {
      "store": 2331,
      "name": "Red Cliffs Mall"
    },
    {
      "store": 2626,
      "name": "University Crossings Plaza"
    },
    {
      "store": 2639,
      "name": "Ft. Union"
    },
    {
      "store": 2811,
      "name": "The Shops at South Town"
    },
    {
      "store": 2907,
      "name": "Layton"
    },
    {
      "store": 2941,
      "name": "Sugarhouse"
    }
  ],
  "VA": [
    {
      "store": 2026,
      "name": "Lynchburg"
    },
    {
      "store": 2029,
      "name": "Short Pump"
    },
    {
      "store": 2065,
      "name": "Creeks @ VA Ctr."
    },
    {
      "store": 2067,
      "name": "Christiansburg"
    },
    {
      "store": 2068,
      "name": "Clarendon Market Common"
    },
    {
      "store": 2131,
      "name": "Harrisonburg Crossing"
    },
    {
      "store": 2195,
      "name": "Lynnhaven Mall"
    },
    {
      "store": 2238,
      "name": "Tysons Corner Mall"
    },
    {
      "store": 2265,
      "name": "New Town Shops"
    },
    {
      "store": 2294,
      "name": "Roanoke"
    },
    {
      "store": 2309,
      "name": "Peninsula Town Center"
    },
    {
      "store": 2314,
      "name": "Chesterfield Town Center"
    },
    {
      "store": 2369,
      "name": "Central Park"
    },
    {
      "store": 2559,
      "name": "Charlottesville"
    },
    {
      "store": 2637,
      "name": "VA Beach"
    },
    {
      "store": 2712,
      "name": "Seven Corners"
    },
    {
      "store": 2735,
      "name": "Chesapeake"
    },
    {
      "store": 2773,
      "name": "Newport News"
    },
    {
      "store": 2867,
      "name": "Potomac"
    },
    {
      "store": 2870,
      "name": "Tanglewood"
    },
    {
      "store": 2937,
      "name": "Fairfax"
    },
    {
      "store": 2948,
      "name": "Manassas"
    },
    {
      "store": 2982,
      "name": "Springfield"
    },
    {
      "store": 2995,
      "name": "Libbie Place"
    }
  ],
  "VT": [
    {
      "store": 2776,
      "name": "Burlington"
    }
  ],
  "WA": [
    {
      "store": 1915,
      "name": "Bellevue"
    },
    {
      "store": 2218,
      "name": "Westwood Village"
    },
    {
      "store": 2280,
      "name": "Northgate"
    },
    {
      "store": 2281,
      "name": "Kitsap Mall"
    },
    {
      "store": 2530,
      "name": "Crossroads"
    },
    {
      "store": 2607,
      "name": "South Center"
    },
    {
      "store": 2617,
      "name": "Issaquah"
    },
    {
      "store": 2653,
      "name": "Olympia"
    },
    {
      "store": 2679,
      "name": "Vancouver"
    },
    {
      "store": 2682,
      "name": "Bellingham"
    },
    {
      "store": 2706,
      "name": "Federal Way"
    },
    {
      "store": 2832,
      "name": "Lakewood"
    },
    {
      "store": 2879,
      "name": "Columbia Center"
    },
    {
      "store": 2888,
      "name": "Alderwood"
    },
    {
      "store": 2910,
      "name": "Woodinville"
    },
    {
      "store": 2951,
      "name": "Eastside"
    },
    {
      "store": 2957,
      "name": "Downtown"
    },
    {
      "store": 2997,
      "name": "Northtown Mall"
    }
  ],
  "WI": [
    {
      "store": 2037,
      "name": "Southland Center"
    },
    {
      "store": 2085,
      "name": "Green Bay"
    },
    {
      "store": 2125,
      "name": "Wausau"
    },
    {
      "store": 2174,
      "name": "East Towne Mall"
    },
    {
      "store": 2213,
      "name": "Brookfield Mall"
    },
    {
      "store": 2248,
      "name": "Valley View Mall"
    },
    {
      "store": 2252,
      "name": "Bayshore Mall"
    },
    {
      "store": 2594,
      "name": "Greenfield"
    },
    {
      "store": 2720,
      "name": "Madison"
    },
    {
      "store": 2944,
      "name": "Mayfair Mall"
    },
    {
      "store": 2977,
      "name": "Appleton"
    }
  ],
  "WV": [
    {
      "store": 2189,
      "name": "University Town Center"
    }
  ],
  "WY": [
    {
      "store": 2674,
      "name": "Cheyenne"
    }
  ]


};