<?php
/** Private fixed provenance for the new statistics cohort. */
if(!defined('ABSPATH'))exit;
return json_decode(<<<'ZP_STATISTICS_FIXED_JSON'
{
  "version": 1,
  "policy": "statistics-exact-pl1-v1",
  "retrieved_observation_date": "2026-10-11",
  "provider_measurement_date": null,
  "minimum_volume": 150,
  "reports": [
    {
      "id": "pl1-tech127",
      "request": {
        "country_id": "1",
        "detail_level": "extended",
        "keywords": [
          "staging wordpress",
          "wordpress staging",
          "środowisko testowe wordpress",
          "strona testowa wordpress",
          "wordpress multisite",
          "multisite wordpress",
          "sieć witryn wordpress",
          "wiele stron wordpress",
          "ssl wordpress",
          "wordpress ssl",
          "mixed content wordpress",
          "mieszana zawartość wordpress",
          "err too many redirects",
          "too many redirects wordpress",
          "wordpress za dużo przekierowań",
          "pętla przekierowań wordpress",
          "błąd 500 wordpress",
          "wordpress błąd 500",
          "internal server error wordpress",
          "wordpress internal server error",
          "błąd 403 wordpress",
          "wordpress błąd 403",
          "403 forbidden wordpress",
          "wordpress 403 forbidden",
          "mapa strony xml",
          "mapa witryny xml",
          "sitemap wordpress",
          "sitemap xml",
          "adres kanoniczny",
          "kanoniczny url",
          "rel canonical",
          "canonical wordpress",
          "robots txt wordpress",
          "plik robots txt",
          "robots txt",
          "soft 404",
          "błąd soft 404",
          "soft 404 wordpress",
          "pozorny błąd 404",
          "błąd 404 wordpress",
          "wordpress 404",
          "przekierowanie 301 wordpress",
          "niedziałające linki",
          "dane strukturalne",
          "schema org wordpress",
          "schema wordpress",
          "dane strukturalne wordpress",
          "formularz wieloetapowy",
          "formularz wieloetapowy wordpress",
          "formularz krokowy",
          "multi step form wordpress",
          "paginacja woocommerce",
          "woocommerce paginacja",
          "paginacja produktów",
          "infinite scroll woocommerce",
          "filtry produktów woocommerce",
          "filtry woocommerce",
          "filtrowanie produktów woocommerce",
          "woocommerce filtry produktów",
          "woocommerce warianty",
          "warianty woocommerce",
          "woocommerce produkt wariantowy",
          "produkty wariantowe woocommerce",
          "woocommerce wysyłka",
          "woocommerce strefy wysyłki",
          "strefy wysyłki woocommerce",
          "woocommerce brak metod wysyłki",
          "woocommerce magazyn",
          "woocommerce stany magazynowe",
          "stany magazynowe woocommerce",
          "zarządzanie magazynem woocommerce",
          "woocommerce konto klienta",
          "woocommerce zakup bez rejestracji",
          "woocommerce rejestracja klienta",
          "zakup bez rejestracji",
          "wyszukiwarka produktów",
          "wyszukiwarka woocommerce",
          "woocommerce wyszukiwarka",
          "wyszukiwanie produktów woocommerce",
          "ga4 ecommerce",
          "google analytics woocommerce",
          "woocommerce google analytics",
          "google tag manager woocommerce",
          "woocommerce rest api",
          "api woocommerce",
          "woocommerce api",
          "integracja woocommerce erp",
          "webhook woocommerce",
          "woocommerce webhook",
          "webhooks woocommerce",
          "woocommerce webhooks",
          "favicon wordpress",
          "favicon svg",
          "favicon ico",
          "ikona strony wordpress",
          "grafika open graph",
          "miniatura linku",
          "open graph wordpress",
          "open graph",
          "woocommerce waluty",
          "woocommerce wiele walut",
          "wiele walut woocommerce",
          "multicurrency woocommerce",
          "woocommerce nie wysyła maili",
          "woocommerce wiadomości email",
          "maile woocommerce",
          "woocommerce email zamówienia",
          "lcp wordpress",
          "largest contentful paint",
          "optymalizacja lcp",
          "lcp strony",
          "inp wordpress",
          "interaction to next paint",
          "optymalizacja inp",
          "inp strony",
          "cls wordpress",
          "cumulative layout shift",
          "optymalizacja cls",
          "cls strony",
          "menu restauracji online",
          "menu online restauracja",
          "menu qr restauracja",
          "cyfrowe menu restauracji",
          "galeria wordpress",
          "galeria zdjęć wordpress",
          "wordpress galeria zdjęć",
          "galeria na stronie internetowej"
        ],
        "limit": 127,
        "page": 1
      },
      "public_id": "8492f7c0a6c1b88f2da91d9bb893ddcd",
      "retrieved_observation_date": "2026-10-11",
      "provider_measurement_date": null,
      "snapshots": [
        {
          "source_file": "native-statistics127-page1.json",
          "source_sha256": "69b07374a1043844cb25dd3da8fb482152c2acdd176ded1d3c09b628ea224e5c",
          "response": {
            "summary": {
              "returns": "one row per requested phrase — searches is that phrase's own volume, never a related phrase's",
              "country_id": "1",
              "public_id": "8492f7c0a6c1b88f2da91d9bb893ddcd",
              "keywords_requested": 127,
              "keywords_found": 52,
              "keywords_with_volume": 52,
              "keywords_found_without_data": 0,
              "keywords_not_found": 48,
              "not_found": [
                "wordpress staging",
                "środowisko testowe wordpress",
                "strona testowa wordpress",
                "sieć witryn wordpress",
                "wiele stron wordpress",
                "mieszana zawartość wordpress",
                "err too many redirects",
                "too many redirects wordpress",
                "wordpress za dużo przekierowań",
                "pętla przekierowań wordpress",
                "wordpress internal server error",
                "błąd 403 wordpress",
                "wordpress błąd 403",
                "błąd soft 404",
                "niedziałające linki",
                "formularz wieloetapowy",
                "formularz wieloetapowy wordpress",
                "formularz krokowy",
                "paginacja woocommerce",
                "woocommerce paginacja",
                "paginacja produktów",
                "filtry produktów woocommerce",
                "filtry woocommerce",
                "filtrowanie produktów woocommerce",
                "woocommerce filtry produktów",
                "warianty woocommerce",
                "woocommerce produkt wariantowy",
                "produkty wariantowe woocommerce",
                "woocommerce strefy wysyłki",
                "strefy wysyłki woocommerce",
                "woocommerce brak metod wysyłki",
                "woocommerce stany magazynowe",
                "stany magazynowe woocommerce",
                "zarządzanie magazynem woocommerce",
                "woocommerce konto klienta",
                "woocommerce zakup bez rejestracji",
                "zakup bez rejestracji",
                "wyszukiwarka woocommerce",
                "woocommerce wyszukiwarka",
                "wyszukiwanie produktów woocommerce",
                "google analytics woocommerce",
                "woocommerce google analytics",
                "integracja woocommerce erp",
                "webhook woocommerce",
                "woocommerce webhook",
                "grafika open graph",
                "miniatura linku",
                "woocommerce waluty"
              ],
              "keywords_not_on_this_page": 27,
              "not_on_this_page": [
                "woocommerce wiele walut",
                "wiele walut woocommerce",
                "multicurrency woocommerce",
                "woocommerce nie wysyła maili",
                "woocommerce wiadomości email",
                "maile woocommerce",
                "woocommerce email zamówienia",
                "lcp wordpress",
                "largest contentful paint",
                "optymalizacja lcp",
                "lcp strony",
                "inp wordpress",
                "interaction to next paint",
                "optymalizacja inp",
                "inp strony",
                "cls wordpress",
                "cumulative layout shift",
                "optymalizacja cls",
                "cls strony",
                "menu restauracji online",
                "menu online restauracja",
                "menu qr restauracja",
                "cyfrowe menu restauracji",
                "galeria wordpress",
                "galeria zdjęć wordpress",
                "wordpress galeria zdjęć",
                "galeria na stronie internetowej"
              ],
              "api_progress": {
                "all_keywords": 127,
                "fetched_keywords": 58
              }
            },
            "all_keywords": [
              {
                "keyword": "staging wordpress",
                "searches": 70,
                "cpc": 5.39,
                "words_count": 2,
                "trend_1": 70,
                "trend_2": 70,
                "trend_3": 70,
                "trend_4": 70,
                "trend_5": 70,
                "trend_6": 70,
                "trend_7": 70,
                "trend_8": 90,
                "trend_9": 70,
                "trend_10": 70,
                "trend_11": 70,
                "trend_12": 110,
                "trends": [
                  70,
                  70,
                  70,
                  70,
                  70,
                  70,
                  70,
                  90,
                  70,
                  70,
                  70,
                  110
                ],
                "serp_params": [
                  "adwords",
                  "people_also_ask",
                  "video_thumbs"
                ],
                "statistics": {
                  "snippets": {
                    "current": [
                      "adwords",
                      "people_also_ask",
                      "video_thumbs"
                    ]
                  },
                  "searches": {
                    "current": 70
                  },
                  "cpc": {
                    "current": 5.39
                  },
                  "trends": {
                    "history": [
                      70,
                      70,
                      70,
                      70,
                      70,
                      70,
                      70,
                      90,
                      70,
                      70,
                      70,
                      110
                    ]
                  }
                }
              },
              {
                "keyword": "wordpress staging",
                "searches": null,
                "cpc": null,
                "words_count": null,
                "trend_1": null,
                "trend_2": null,
                "trend_3": null,
                "trend_4": null,
                "trend_5": null,
                "trend_6": null,
                "trend_7": null,
                "trend_8": null,
                "trend_9": null,
                "trend_10": null,
                "trend_11": null,
                "trend_12": null,
                "trends": [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": [],
                  "cpc": [],
                  "trends": {
                    "history": [
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0
                    ]
                  }
                }
              },
              {
                "keyword": "środowisko testowe wordpress",
                "searches": null,
                "cpc": null,
                "words_count": null,
                "trend_1": null,
                "trend_2": null,
                "trend_3": null,
                "trend_4": null,
                "trend_5": null,
                "trend_6": null,
                "trend_7": null,
                "trend_8": null,
                "trend_9": null,
                "trend_10": null,
                "trend_11": null,
                "trend_12": null,
                "trends": [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": [],
                  "cpc": [],
                  "trends": {
                    "history": [
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0
                    ]
                  }
                }
              },
              {
                "keyword": "strona testowa wordpress",
                "searches": null,
                "cpc": null,
                "words_count": null,
                "trend_1": null,
                "trend_2": null,
                "trend_3": null,
                "trend_4": null,
                "trend_5": null,
                "trend_6": null,
                "trend_7": null,
                "trend_8": null,
                "trend_9": null,
                "trend_10": null,
                "trend_11": null,
                "trend_12": null,
                "trends": [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": [],
                  "cpc": [],
                  "trends": {
                    "history": [
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0
                    ]
                  }
                }
              },
              {
                "keyword": "wordpress multisite",
                "searches": 320,
                "cpc": 2.21,
                "words_count": 2,
                "trend_1": 390,
                "trend_2": 390,
                "trend_3": 390,
                "trend_4": 320,
                "trend_5": 320,
                "trend_6": 260,
                "trend_7": 260,
                "trend_8": 260,
                "trend_9": 260,
                "trend_10": 320,
                "trend_11": 320,
                "trend_12": 260,
                "trends": [
                  390,
                  390,
                  390,
                  320,
                  320,
                  260,
                  260,
                  260,
                  260,
                  320,
                  320,
                  260
                ],
                "serp_params": [
                  "adwords",
                  "video_thumbs"
                ],
                "statistics": {
                  "snippets": {
                    "current": [
                      "adwords",
                      "video_thumbs"
                    ]
                  },
                  "searches": {
                    "current": 320
                  },
                  "cpc": {
                    "current": 2.21
                  },
                  "trends": {
                    "history": [
                      390,
                      390,
                      390,
                      320,
                      320,
                      260,
                      260,
                      260,
                      260,
                      320,
                      320,
                      260
                    ]
                  }
                }
              },
              {
                "keyword": "multisite wordpress",
                "searches": 320,
                "cpc": 2.22,
                "words_count": 2,
                "trend_1": 390,
                "trend_2": 390,
                "trend_3": 390,
                "trend_4": 320,
                "trend_5": 320,
                "trend_6": 260,
                "trend_7": 260,
                "trend_8": 260,
                "trend_9": 260,
                "trend_10": 320,
                "trend_11": 320,
                "trend_12": 260,
                "trends": [
                  390,
                  390,
                  390,
                  320,
                  320,
                  260,
                  260,
                  260,
                  260,
                  320,
                  320,
                  260
                ],
                "serp_params": [
                  "image_thumbs",
                  "people_also_ask",
                  "video_thumbs"
                ],
                "statistics": {
                  "snippets": {
                    "current": [
                      "image_thumbs",
                      "people_also_ask",
                      "video_thumbs"
                    ]
                  },
                  "searches": {
                    "current": 320
                  },
                  "cpc": {
                    "current": 2.22
                  },
                  "trends": {
                    "history": [
                      390,
                      390,
                      390,
                      320,
                      320,
                      260,
                      260,
                      260,
                      260,
                      320,
                      320,
                      260
                    ]
                  }
                }
              },
              {
                "keyword": "sieć witryn wordpress",
                "searches": null,
                "cpc": null,
                "words_count": null,
                "trend_1": null,
                "trend_2": null,
                "trend_3": null,
                "trend_4": null,
                "trend_5": null,
                "trend_6": null,
                "trend_7": null,
                "trend_8": null,
                "trend_9": null,
                "trend_10": null,
                "trend_11": null,
                "trend_12": null,
                "trends": [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": [],
                  "cpc": [],
                  "trends": {
                    "history": [
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0
                    ]
                  }
                }
              },
              {
                "keyword": "wiele stron wordpress",
                "searches": null,
                "cpc": null,
                "words_count": null,
                "trend_1": null,
                "trend_2": null,
                "trend_3": null,
                "trend_4": null,
                "trend_5": null,
                "trend_6": null,
                "trend_7": null,
                "trend_8": null,
                "trend_9": null,
                "trend_10": null,
                "trend_11": null,
                "trend_12": null,
                "trends": [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": [],
                  "cpc": [],
                  "trends": {
                    "history": [
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0
                    ]
                  }
                }
              },
              {
                "keyword": "ssl wordpress",
                "searches": 170,
                "cpc": 0.77,
                "words_count": 2,
                "trend_1": 210,
                "trend_2": 210,
                "trend_3": 260,
                "trend_4": 170,
                "trend_5": 210,
                "trend_6": 210,
                "trend_7": 170,
                "trend_8": 170,
                "trend_9": 140,
                "trend_10": 170,
                "trend_11": 170,
                "trend_12": 170,
                "trends": [
                  210,
                  210,
                  260,
                  170,
                  210,
                  210,
                  170,
                  170,
                  140,
                  170,
                  170,
                  170
                ],
                "serp_params": [
                  "video_thumbs"
                ],
                "statistics": {
                  "snippets": {
                    "current": [
                      "video_thumbs"
                    ]
                  },
                  "searches": {
                    "current": 170
                  },
                  "cpc": {
                    "current": 0.77
                  },
                  "trends": {
                    "history": [
                      210,
                      210,
                      260,
                      170,
                      210,
                      210,
                      170,
                      170,
                      140,
                      170,
                      170,
                      170
                    ]
                  }
                }
              },
              {
                "keyword": "wordpress ssl",
                "searches": 170,
                "cpc": 5.09,
                "words_count": 2,
                "trend_1": 210,
                "trend_2": 210,
                "trend_3": 260,
                "trend_4": 170,
                "trend_5": 170,
                "trend_6": 170,
                "trend_7": 140,
                "trend_8": 170,
                "trend_9": 140,
                "trend_10": 170,
                "trend_11": 170,
                "trend_12": 170,
                "trends": [
                  210,
                  210,
                  260,
                  170,
                  170,
                  170,
                  140,
                  170,
                  140,
                  170,
                  170,
                  170
                ],
                "serp_params": [
                  "image_thumbs",
                  "video_thumbs"
                ],
                "statistics": {
                  "snippets": {
                    "current": [
                      "image_thumbs",
                      "video_thumbs"
                    ]
                  },
                  "searches": {
                    "current": 170
                  },
                  "cpc": {
                    "current": 5.09
                  },
                  "trends": {
                    "history": [
                      210,
                      210,
                      260,
                      170,
                      170,
                      170,
                      140,
                      170,
                      140,
                      170,
                      170,
                      170
                    ]
                  }
                }
              },
              {
                "keyword": "mixed content wordpress",
                "searches": 20,
                "cpc": 0,
                "words_count": 3,
                "trend_1": 20,
                "trend_2": 20,
                "trend_3": 20,
                "trend_4": 20,
                "trend_5": 10,
                "trend_6": 20,
                "trend_7": 20,
                "trend_8": 20,
                "trend_9": 20,
                "trend_10": 20,
                "trend_11": 10,
                "trend_12": 20,
                "trends": [
                  20,
                  20,
                  20,
                  20,
                  10,
                  20,
                  20,
                  20,
                  20,
                  20,
                  10,
                  20
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": {
                    "current": 20
                  },
                  "cpc": {
                    "current": 0
                  },
                  "trends": {
                    "history": [
                      20,
                      20,
                      20,
                      20,
                      10,
                      20,
                      20,
                      20,
                      20,
                      20,
                      10,
                      20
                    ]
                  }
                }
              },
              {
                "keyword": "mieszana zawartość wordpress",
                "searches": null,
                "cpc": null,
                "words_count": null,
                "trend_1": null,
                "trend_2": null,
                "trend_3": null,
                "trend_4": null,
                "trend_5": null,
                "trend_6": null,
                "trend_7": null,
                "trend_8": null,
                "trend_9": null,
                "trend_10": null,
                "trend_11": null,
                "trend_12": null,
                "trends": [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": [],
                  "cpc": [],
                  "trends": {
                    "history": [
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0
                    ]
                  }
                }
              },
              {
                "keyword": "err too many redirects",
                "searches": null,
                "cpc": null,
                "words_count": null,
                "trend_1": null,
                "trend_2": null,
                "trend_3": null,
                "trend_4": null,
                "trend_5": null,
                "trend_6": null,
                "trend_7": null,
                "trend_8": null,
                "trend_9": null,
                "trend_10": null,
                "trend_11": null,
                "trend_12": null,
                "trends": [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": [],
                  "cpc": [],
                  "trends": {
                    "history": [
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0
                    ]
                  }
                }
              },
              {
                "keyword": "too many redirects wordpress",
                "searches": null,
                "cpc": null,
                "words_count": null,
                "trend_1": null,
                "trend_2": null,
                "trend_3": null,
                "trend_4": null,
                "trend_5": null,
                "trend_6": null,
                "trend_7": null,
                "trend_8": null,
                "trend_9": null,
                "trend_10": null,
                "trend_11": null,
                "trend_12": null,
                "trends": [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": [],
                  "cpc": [],
                  "trends": {
                    "history": [
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0
                    ]
                  }
                }
              },
              {
                "keyword": "wordpress za dużo przekierowań",
                "searches": null,
                "cpc": null,
                "words_count": null,
                "trend_1": null,
                "trend_2": null,
                "trend_3": null,
                "trend_4": null,
                "trend_5": null,
                "trend_6": null,
                "trend_7": null,
                "trend_8": null,
                "trend_9": null,
                "trend_10": null,
                "trend_11": null,
                "trend_12": null,
                "trends": [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": [],
                  "cpc": [],
                  "trends": {
                    "history": [
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0
                    ]
                  }
                }
              },
              {
                "keyword": "pętla przekierowań wordpress",
                "searches": null,
                "cpc": null,
                "words_count": null,
                "trend_1": null,
                "trend_2": null,
                "trend_3": null,
                "trend_4": null,
                "trend_5": null,
                "trend_6": null,
                "trend_7": null,
                "trend_8": null,
                "trend_9": null,
                "trend_10": null,
                "trend_11": null,
                "trend_12": null,
                "trends": [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": [],
                  "cpc": [],
                  "trends": {
                    "history": [
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0
                    ]
                  }
                }
              },
              {
                "keyword": "błąd 500 wordpress",
                "searches": 20,
                "cpc": 0,
                "words_count": 3,
                "trend_1": 10,
                "trend_2": 10,
                "trend_3": 10,
                "trend_4": 10,
                "trend_5": 10,
                "trend_6": 10,
                "trend_7": 10,
                "trend_8": 10,
                "trend_9": 40,
                "trend_10": 70,
                "trend_11": 50,
                "trend_12": 50,
                "trends": [
                  10,
                  10,
                  10,
                  10,
                  10,
                  10,
                  10,
                  10,
                  40,
                  70,
                  50,
                  50
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": {
                    "current": 20
                  },
                  "cpc": {
                    "current": 0
                  },
                  "trends": {
                    "history": [
                      10,
                      10,
                      10,
                      10,
                      10,
                      10,
                      10,
                      10,
                      40,
                      70,
                      50,
                      50
                    ]
                  }
                }
              },
              {
                "keyword": "wordpress błąd 500",
                "searches": 20,
                "cpc": 0,
                "words_count": 3,
                "trend_1": 10,
                "trend_2": 10,
                "trend_3": 10,
                "trend_4": 10,
                "trend_5": 10,
                "trend_6": 0,
                "trend_7": 10,
                "trend_8": 10,
                "trend_9": 40,
                "trend_10": 90,
                "trend_11": 30,
                "trend_12": 40,
                "trends": [
                  10,
                  10,
                  10,
                  10,
                  10,
                  0,
                  10,
                  10,
                  40,
                  90,
                  30,
                  40
                ],
                "serp_params": [
                  "featured_answer"
                ],
                "statistics": {
                  "snippets": {
                    "current": [
                      "featured_answer"
                    ]
                  },
                  "searches": {
                    "current": 20
                  },
                  "cpc": {
                    "current": 0
                  },
                  "trends": {
                    "history": [
                      10,
                      10,
                      10,
                      10,
                      10,
                      0,
                      10,
                      10,
                      40,
                      90,
                      30,
                      40
                    ]
                  }
                }
              },
              {
                "keyword": "internal server error wordpress",
                "searches": 0,
                "cpc": 0,
                "words_count": 4,
                "trend_1": -1,
                "trend_2": -1,
                "trend_3": -1,
                "trend_4": -1,
                "trend_5": -1,
                "trend_6": -1,
                "trend_7": -1,
                "trend_8": -1,
                "trend_9": -1,
                "trend_10": -1,
                "trend_11": -1,
                "trend_12": -1,
                "trends": [
                  -1,
                  -1,
                  -1,
                  -1,
                  -1,
                  -1,
                  -1,
                  -1,
                  -1,
                  -1,
                  -1,
                  -1
                ],
                "serp_params": [
                  "people_also_ask",
                  "video_thumbs"
                ],
                "statistics": {
                  "snippets": {
                    "current": [
                      "people_also_ask",
                      "video_thumbs"
                    ]
                  },
                  "searches": {
                    "current": 0
                  },
                  "cpc": {
                    "current": 0
                  },
                  "trends": {
                    "history": [
                      -1,
                      -1,
                      -1,
                      -1,
                      -1,
                      -1,
                      -1,
                      -1,
                      -1,
                      -1,
                      -1,
                      -1
                    ]
                  }
                }
              },
              {
                "keyword": "wordpress internal server error",
                "searches": null,
                "cpc": null,
                "words_count": null,
                "trend_1": null,
                "trend_2": null,
                "trend_3": null,
                "trend_4": null,
                "trend_5": null,
                "trend_6": null,
                "trend_7": null,
                "trend_8": null,
                "trend_9": null,
                "trend_10": null,
                "trend_11": null,
                "trend_12": null,
                "trends": [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": [],
                  "cpc": [],
                  "trends": {
                    "history": [
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0
                    ]
                  }
                }
              },
              {
                "keyword": "błąd 403 wordpress",
                "searches": null,
                "cpc": null,
                "words_count": null,
                "trend_1": null,
                "trend_2": null,
                "trend_3": null,
                "trend_4": null,
                "trend_5": null,
                "trend_6": null,
                "trend_7": null,
                "trend_8": null,
                "trend_9": null,
                "trend_10": null,
                "trend_11": null,
                "trend_12": null,
                "trends": [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": [],
                  "cpc": [],
                  "trends": {
                    "history": [
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0
                    ]
                  }
                }
              },
              {
                "keyword": "wordpress błąd 403",
                "searches": null,
                "cpc": null,
                "words_count": null,
                "trend_1": null,
                "trend_2": null,
                "trend_3": null,
                "trend_4": null,
                "trend_5": null,
                "trend_6": null,
                "trend_7": null,
                "trend_8": null,
                "trend_9": null,
                "trend_10": null,
                "trend_11": null,
                "trend_12": null,
                "trends": [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": [],
                  "cpc": [],
                  "trends": {
                    "history": [
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0
                    ]
                  }
                }
              },
              {
                "keyword": "403 forbidden wordpress",
                "searches": 30,
                "cpc": 0,
                "words_count": 3,
                "trend_1": 30,
                "trend_2": 40,
                "trend_3": 40,
                "trend_4": 30,
                "trend_5": 30,
                "trend_6": 30,
                "trend_7": 30,
                "trend_8": 20,
                "trend_9": 20,
                "trend_10": 20,
                "trend_11": 20,
                "trend_12": 20,
                "trends": [
                  30,
                  40,
                  40,
                  30,
                  30,
                  30,
                  30,
                  20,
                  20,
                  20,
                  20,
                  20
                ],
                "serp_params": [
                  "people_also_ask",
                  "video_thumbs"
                ],
                "statistics": {
                  "snippets": {
                    "current": [
                      "people_also_ask",
                      "video_thumbs"
                    ]
                  },
                  "searches": {
                    "current": 30
                  },
                  "cpc": {
                    "current": 0
                  },
                  "trends": {
                    "history": [
                      30,
                      40,
                      40,
                      30,
                      30,
                      30,
                      30,
                      20,
                      20,
                      20,
                      20,
                      20
                    ]
                  }
                }
              },
              {
                "keyword": "wordpress 403 forbidden",
                "searches": 30,
                "cpc": 2.86,
                "words_count": 3,
                "trend_1": 30,
                "trend_2": 50,
                "trend_3": 20,
                "trend_4": 30,
                "trend_5": 20,
                "trend_6": 20,
                "trend_7": 30,
                "trend_8": 30,
                "trend_9": 30,
                "trend_10": 30,
                "trend_11": 30,
                "trend_12": 30,
                "trends": [
                  30,
                  50,
                  20,
                  30,
                  20,
                  20,
                  30,
                  30,
                  30,
                  30,
                  30,
                  30
                ],
                "serp_params": [
                  "video_thumbs"
                ],
                "statistics": {
                  "snippets": {
                    "current": [
                      "video_thumbs"
                    ]
                  },
                  "searches": {
                    "current": 30
                  },
                  "cpc": {
                    "current": 2.86
                  },
                  "trends": {
                    "history": [
                      30,
                      50,
                      20,
                      30,
                      20,
                      20,
                      30,
                      30,
                      30,
                      30,
                      30,
                      30
                    ]
                  }
                }
              },
              {
                "keyword": "mapa strony xml",
                "searches": 20,
                "cpc": 0,
                "words_count": 3,
                "trend_1": 30,
                "trend_2": 30,
                "trend_3": 30,
                "trend_4": 10,
                "trend_5": 10,
                "trend_6": 10,
                "trend_7": 20,
                "trend_8": 10,
                "trend_9": 10,
                "trend_10": 10,
                "trend_11": 10,
                "trend_12": 20,
                "trends": [
                  30,
                  30,
                  30,
                  10,
                  10,
                  10,
                  20,
                  10,
                  10,
                  10,
                  10,
                  20
                ],
                "serp_params": [
                  "image_thumbs",
                  "wiki_right"
                ],
                "statistics": {
                  "snippets": {
                    "current": [
                      "image_thumbs",
                      "wiki_right"
                    ]
                  },
                  "searches": {
                    "current": 20
                  },
                  "cpc": {
                    "current": 0
                  },
                  "trends": {
                    "history": [
                      30,
                      30,
                      30,
                      10,
                      10,
                      10,
                      20,
                      10,
                      10,
                      10,
                      10,
                      20
                    ]
                  }
                }
              },
              {
                "keyword": "mapa witryny xml",
                "searches": 10,
                "cpc": 0,
                "words_count": 3,
                "trend_1": 10,
                "trend_2": 10,
                "trend_3": 10,
                "trend_4": 10,
                "trend_5": 10,
                "trend_6": 10,
                "trend_7": 10,
                "trend_8": 10,
                "trend_9": 10,
                "trend_10": 10,
                "trend_11": 10,
                "trend_12": 10,
                "trends": [
                  10,
                  10,
                  10,
                  10,
                  10,
                  10,
                  10,
                  10,
                  10,
                  10,
                  10,
                  10
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": {
                    "current": 10
                  },
                  "cpc": {
                    "current": 0
                  },
                  "trends": {
                    "history": [
                      10,
                      10,
                      10,
                      10,
                      10,
                      10,
                      10,
                      10,
                      10,
                      10,
                      10,
                      10
                    ]
                  }
                }
              },
              {
                "keyword": "sitemap wordpress",
                "searches": 140,
                "cpc": 1.62,
                "words_count": 2,
                "trend_1": 140,
                "trend_2": 110,
                "trend_3": 110,
                "trend_4": 110,
                "trend_5": 110,
                "trend_6": 110,
                "trend_7": 110,
                "trend_8": 140,
                "trend_9": 210,
                "trend_10": 210,
                "trend_11": 110,
                "trend_12": 110,
                "trends": [
                  140,
                  110,
                  110,
                  110,
                  110,
                  110,
                  110,
                  140,
                  210,
                  210,
                  110,
                  110
                ],
                "serp_params": [
                  "video_thumbs"
                ],
                "statistics": {
                  "snippets": {
                    "current": [
                      "video_thumbs"
                    ]
                  },
                  "searches": {
                    "current": 140
                  },
                  "cpc": {
                    "current": 1.62
                  },
                  "trends": {
                    "history": [
                      140,
                      110,
                      110,
                      110,
                      110,
                      110,
                      110,
                      140,
                      210,
                      210,
                      110,
                      110
                    ]
                  }
                }
              },
              {
                "keyword": "sitemap xml",
                "searches": 480,
                "cpc": 0.48,
                "words_count": 2,
                "trend_1": 480,
                "trend_2": 480,
                "trend_3": 590,
                "trend_4": 480,
                "trend_5": 480,
                "trend_6": 390,
                "trend_7": 480,
                "trend_8": 390,
                "trend_9": 320,
                "trend_10": 390,
                "trend_11": 480,
                "trend_12": 390,
                "trends": [
                  480,
                  480,
                  590,
                  480,
                  480,
                  390,
                  480,
                  390,
                  320,
                  390,
                  480,
                  390
                ],
                "serp_params": [
                  "people_also_ask",
                  "wiki_right"
                ],
                "statistics": {
                  "snippets": {
                    "current": [
                      "people_also_ask",
                      "wiki_right"
                    ]
                  },
                  "searches": {
                    "current": 480
                  },
                  "cpc": {
                    "current": 0.48
                  },
                  "trends": {
                    "history": [
                      480,
                      480,
                      590,
                      480,
                      480,
                      390,
                      480,
                      390,
                      320,
                      390,
                      480,
                      390
                    ]
                  }
                }
              },
              {
                "keyword": "adres kanoniczny",
                "searches": 50,
                "cpc": 0,
                "words_count": 2,
                "trend_1": 40,
                "trend_2": 30,
                "trend_3": 30,
                "trend_4": 30,
                "trend_5": 30,
                "trend_6": 40,
                "trend_7": 20,
                "trend_8": 40,
                "trend_9": 40,
                "trend_10": 90,
                "trend_11": 90,
                "trend_12": 50,
                "trends": [
                  40,
                  30,
                  30,
                  30,
                  30,
                  40,
                  20,
                  40,
                  40,
                  90,
                  90,
                  50
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": {
                    "current": 50
                  },
                  "cpc": {
                    "current": 0
                  },
                  "trends": {
                    "history": [
                      40,
                      30,
                      30,
                      30,
                      30,
                      40,
                      20,
                      40,
                      40,
                      90,
                      90,
                      50
                    ]
                  }
                }
              },
              {
                "keyword": "kanoniczny url",
                "searches": 40,
                "cpc": 0,
                "words_count": 2,
                "trend_1": 40,
                "trend_2": 30,
                "trend_3": 20,
                "trend_4": 30,
                "trend_5": 30,
                "trend_6": 30,
                "trend_7": 20,
                "trend_8": 40,
                "trend_9": 40,
                "trend_10": 90,
                "trend_11": 70,
                "trend_12": 40,
                "trends": [
                  40,
                  30,
                  20,
                  30,
                  30,
                  30,
                  20,
                  40,
                  40,
                  90,
                  70,
                  40
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": {
                    "current": 40
                  },
                  "cpc": {
                    "current": 0
                  },
                  "trends": {
                    "history": [
                      40,
                      30,
                      20,
                      30,
                      30,
                      30,
                      20,
                      40,
                      40,
                      90,
                      70,
                      40
                    ]
                  }
                }
              },
              {
                "keyword": "rel canonical",
                "searches": 110,
                "cpc": 0.23,
                "words_count": 2,
                "trend_1": 110,
                "trend_2": 70,
                "trend_3": 50,
                "trend_4": 70,
                "trend_5": 90,
                "trend_6": 90,
                "trend_7": 70,
                "trend_8": 110,
                "trend_9": 140,
                "trend_10": 260,
                "trend_11": 170,
                "trend_12": 90,
                "trends": [
                  110,
                  70,
                  50,
                  70,
                  90,
                  90,
                  70,
                  110,
                  140,
                  260,
                  170,
                  90
                ],
                "serp_params": [
                  "wiki_right"
                ],
                "statistics": {
                  "snippets": {
                    "current": [
                      "wiki_right"
                    ]
                  },
                  "searches": {
                    "current": 110
                  },
                  "cpc": {
                    "current": 0.23
                  },
                  "trends": {
                    "history": [
                      110,
                      70,
                      50,
                      70,
                      90,
                      90,
                      70,
                      110,
                      140,
                      260,
                      170,
                      90
                    ]
                  }
                }
              },
              {
                "keyword": "canonical wordpress",
                "searches": 10,
                "cpc": 0,
                "words_count": 2,
                "trend_1": 10,
                "trend_2": 10,
                "trend_3": 10,
                "trend_4": 10,
                "trend_5": 10,
                "trend_6": 10,
                "trend_7": 10,
                "trend_8": 10,
                "trend_9": 10,
                "trend_10": 10,
                "trend_11": 10,
                "trend_12": 10,
                "trends": [
                  10,
                  10,
                  10,
                  10,
                  10,
                  10,
                  10,
                  10,
                  10,
                  10,
                  10,
                  10
                ],
                "serp_params": [
                  "people_also_ask",
                  "site_links",
                  "video_thumbs"
                ],
                "statistics": {
                  "snippets": {
                    "current": [
                      "people_also_ask",
                      "site_links",
                      "video_thumbs"
                    ]
                  },
                  "searches": {
                    "current": 10
                  },
                  "cpc": {
                    "current": 0
                  },
                  "trends": {
                    "history": [
                      10,
                      10,
                      10,
                      10,
                      10,
                      10,
                      10,
                      10,
                      10,
                      10,
                      10,
                      10
                    ]
                  }
                }
              },
              {
                "keyword": "robots txt wordpress",
                "searches": 140,
                "cpc": 2.92,
                "words_count": 3,
                "trend_1": 140,
                "trend_2": 90,
                "trend_3": 110,
                "trend_4": 90,
                "trend_5": 110,
                "trend_6": 90,
                "trend_7": 90,
                "trend_8": 140,
                "trend_9": 140,
                "trend_10": 210,
                "trend_11": 140,
                "trend_12": 140,
                "trends": [
                  140,
                  90,
                  110,
                  90,
                  110,
                  90,
                  90,
                  140,
                  140,
                  210,
                  140,
                  140
                ],
                "serp_params": [
                  "adwords",
                  "video_thumbs"
                ],
                "statistics": {
                  "snippets": {
                    "current": [
                      "adwords",
                      "video_thumbs"
                    ]
                  },
                  "searches": {
                    "current": 140
                  },
                  "cpc": {
                    "current": 2.92
                  },
                  "trends": {
                    "history": [
                      140,
                      90,
                      110,
                      90,
                      110,
                      90,
                      90,
                      140,
                      140,
                      210,
                      140,
                      140
                    ]
                  }
                }
              },
              {
                "keyword": "plik robots txt",
                "searches": 90,
                "cpc": 0.59,
                "words_count": 3,
                "trend_1": 70,
                "trend_2": 70,
                "trend_3": 90,
                "trend_4": 70,
                "trend_5": 110,
                "trend_6": 140,
                "trend_7": 170,
                "trend_8": 140,
                "trend_9": 90,
                "trend_10": 50,
                "trend_11": 40,
                "trend_12": 40,
                "trends": [
                  70,
                  70,
                  90,
                  70,
                  110,
                  140,
                  170,
                  140,
                  90,
                  50,
                  40,
                  40
                ],
                "serp_params": [
                  "people_also_ask",
                  "video_thumbs"
                ],
                "statistics": {
                  "snippets": {
                    "current": [
                      "people_also_ask",
                      "video_thumbs"
                    ]
                  },
                  "searches": {
                    "current": 90
                  },
                  "cpc": {
                    "current": 0.59
                  },
                  "trends": {
                    "history": [
                      70,
                      70,
                      90,
                      70,
                      110,
                      140,
                      170,
                      140,
                      90,
                      50,
                      40,
                      40
                    ]
                  }
                }
              },
              {
                "keyword": "robots txt",
                "searches": 1000,
                "cpc": 0.44,
                "words_count": 2,
                "trend_1": 1000,
                "trend_2": 1000,
                "trend_3": 880,
                "trend_4": 720,
                "trend_5": 880,
                "trend_6": 880,
                "trend_7": 720,
                "trend_8": 1900,
                "trend_9": 1300,
                "trend_10": 1300,
                "trend_11": 1300,
                "trend_12": 880,
                "trends": [
                  1000,
                  1000,
                  880,
                  720,
                  880,
                  880,
                  720,
                  1900,
                  1300,
                  1300,
                  1300,
                  880
                ],
                "serp_params": [
                  "people_also_ask",
                  "video_thumbs",
                  "wiki_right"
                ],
                "statistics": {
                  "snippets": {
                    "current": [
                      "people_also_ask",
                      "video_thumbs",
                      "wiki_right"
                    ]
                  },
                  "searches": {
                    "current": 1000
                  },
                  "cpc": {
                    "current": 0.44
                  },
                  "trends": {
                    "history": [
                      1000,
                      1000,
                      880,
                      720,
                      880,
                      880,
                      720,
                      1900,
                      1300,
                      1300,
                      1300,
                      880
                    ]
                  }
                }
              },
              {
                "keyword": "soft 404",
                "searches": 20,
                "cpc": 0,
                "words_count": 2,
                "trend_1": 20,
                "trend_2": 20,
                "trend_3": 20,
                "trend_4": 20,
                "trend_5": 20,
                "trend_6": 10,
                "trend_7": 20,
                "trend_8": 30,
                "trend_9": 10,
                "trend_10": 30,
                "trend_11": 30,
                "trend_12": 10,
                "trends": [
                  20,
                  20,
                  20,
                  20,
                  20,
                  10,
                  20,
                  30,
                  10,
                  30,
                  30,
                  10
                ],
                "serp_params": [
                  "image_thumbs"
                ],
                "statistics": {
                  "snippets": {
                    "current": [
                      "image_thumbs"
                    ]
                  },
                  "searches": {
                    "current": 20
                  },
                  "cpc": {
                    "current": 0
                  },
                  "trends": {
                    "history": [
                      20,
                      20,
                      20,
                      20,
                      20,
                      10,
                      20,
                      30,
                      10,
                      30,
                      30,
                      10
                    ]
                  }
                }
              },
              {
                "keyword": "błąd soft 404",
                "searches": null,
                "cpc": null,
                "words_count": null,
                "trend_1": null,
                "trend_2": null,
                "trend_3": null,
                "trend_4": null,
                "trend_5": null,
                "trend_6": null,
                "trend_7": null,
                "trend_8": null,
                "trend_9": null,
                "trend_10": null,
                "trend_11": null,
                "trend_12": null,
                "trends": [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": [],
                  "cpc": [],
                  "trends": {
                    "history": [
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0
                    ]
                  }
                }
              },
              {
                "keyword": "soft 404 wordpress",
                "searches": 0,
                "cpc": 0,
                "words_count": 3,
                "trend_1": 0,
                "trend_2": 0,
                "trend_3": 0,
                "trend_4": 0,
                "trend_5": 0,
                "trend_6": 0,
                "trend_7": 0,
                "trend_8": 0,
                "trend_9": 0,
                "trend_10": 0,
                "trend_11": 0,
                "trend_12": 0,
                "trends": [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "serp_params": [
                  "image_thumbs"
                ],
                "statistics": {
                  "snippets": {
                    "current": [
                      "image_thumbs"
                    ]
                  },
                  "searches": {
                    "current": 0
                  },
                  "cpc": {
                    "current": 0
                  },
                  "trends": {
                    "history": [
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0
                    ]
                  }
                }
              },
              {
                "keyword": "pozorny błąd 404",
                "searches": 30,
                "cpc": 0,
                "words_count": 3,
                "trend_1": 30,
                "trend_2": 40,
                "trend_3": 40,
                "trend_4": 30,
                "trend_5": 20,
                "trend_6": 20,
                "trend_7": 20,
                "trend_8": 30,
                "trend_9": 20,
                "trend_10": 30,
                "trend_11": 30,
                "trend_12": 20,
                "trends": [
                  30,
                  40,
                  40,
                  30,
                  20,
                  20,
                  20,
                  30,
                  20,
                  30,
                  30,
                  20
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": {
                    "current": 30
                  },
                  "cpc": {
                    "current": 0
                  },
                  "trends": {
                    "history": [
                      30,
                      40,
                      40,
                      30,
                      20,
                      20,
                      20,
                      30,
                      20,
                      30,
                      30,
                      20
                    ]
                  }
                }
              },
              {
                "keyword": "błąd 404 wordpress",
                "searches": 30,
                "cpc": 0,
                "words_count": 3,
                "trend_1": 20,
                "trend_2": 20,
                "trend_3": 10,
                "trend_4": 20,
                "trend_5": 20,
                "trend_6": 20,
                "trend_7": 10,
                "trend_8": 30,
                "trend_9": 40,
                "trend_10": 90,
                "trend_11": 70,
                "trend_12": 70,
                "trends": [
                  20,
                  20,
                  10,
                  20,
                  20,
                  20,
                  10,
                  30,
                  40,
                  90,
                  70,
                  70
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": {
                    "current": 30
                  },
                  "cpc": {
                    "current": 0
                  },
                  "trends": {
                    "history": [
                      20,
                      20,
                      10,
                      20,
                      20,
                      20,
                      10,
                      30,
                      40,
                      90,
                      70,
                      70
                    ]
                  }
                }
              },
              {
                "keyword": "wordpress 404",
                "searches": 20,
                "cpc": 0,
                "words_count": 2,
                "trend_1": 20,
                "trend_2": 10,
                "trend_3": 10,
                "trend_4": 20,
                "trend_5": 10,
                "trend_6": 10,
                "trend_7": 20,
                "trend_8": 20,
                "trend_9": 20,
                "trend_10": 10,
                "trend_11": 20,
                "trend_12": 10,
                "trends": [
                  20,
                  10,
                  10,
                  20,
                  10,
                  10,
                  20,
                  20,
                  20,
                  10,
                  20,
                  10
                ],
                "serp_params": [
                  "video_thumbs"
                ],
                "statistics": {
                  "snippets": {
                    "current": [
                      "video_thumbs"
                    ]
                  },
                  "searches": {
                    "current": 20
                  },
                  "cpc": {
                    "current": 0
                  },
                  "trends": {
                    "history": [
                      20,
                      10,
                      10,
                      20,
                      10,
                      10,
                      20,
                      20,
                      20,
                      10,
                      20,
                      10
                    ]
                  }
                }
              },
              {
                "keyword": "przekierowanie 301 wordpress",
                "searches": 10,
                "cpc": 2.37,
                "words_count": 3,
                "trend_1": 10,
                "trend_2": 10,
                "trend_3": 20,
                "trend_4": 10,
                "trend_5": 10,
                "trend_6": 10,
                "trend_7": 10,
                "trend_8": 10,
                "trend_9": 10,
                "trend_10": 10,
                "trend_11": 20,
                "trend_12": 10,
                "trends": [
                  10,
                  10,
                  20,
                  10,
                  10,
                  10,
                  10,
                  10,
                  10,
                  10,
                  20,
                  10
                ],
                "serp_params": [
                  "video_thumbs"
                ],
                "statistics": {
                  "snippets": {
                    "current": [
                      "video_thumbs"
                    ]
                  },
                  "searches": {
                    "current": 10
                  },
                  "cpc": {
                    "current": 2.37
                  },
                  "trends": {
                    "history": [
                      10,
                      10,
                      20,
                      10,
                      10,
                      10,
                      10,
                      10,
                      10,
                      10,
                      20,
                      10
                    ]
                  }
                }
              },
              {
                "keyword": "niedziałające linki",
                "searches": null,
                "cpc": null,
                "words_count": null,
                "trend_1": null,
                "trend_2": null,
                "trend_3": null,
                "trend_4": null,
                "trend_5": null,
                "trend_6": null,
                "trend_7": null,
                "trend_8": null,
                "trend_9": null,
                "trend_10": null,
                "trend_11": null,
                "trend_12": null,
                "trends": [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": [],
                  "cpc": [],
                  "trends": {
                    "history": [
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0
                    ]
                  }
                }
              },
              {
                "keyword": "dane strukturalne",
                "searches": 170,
                "cpc": 0,
                "words_count": 2,
                "trend_1": 170,
                "trend_2": 170,
                "trend_3": 140,
                "trend_4": 140,
                "trend_5": 170,
                "trend_6": 170,
                "trend_7": 110,
                "trend_8": 170,
                "trend_9": 170,
                "trend_10": 210,
                "trend_11": 210,
                "trend_12": 170,
                "trends": [
                  170,
                  170,
                  140,
                  140,
                  170,
                  170,
                  110,
                  170,
                  170,
                  210,
                  210,
                  170
                ],
                "serp_params": [
                  "featured_answer"
                ],
                "statistics": {
                  "snippets": {
                    "current": [
                      "featured_answer"
                    ]
                  },
                  "searches": {
                    "current": 170
                  },
                  "cpc": {
                    "current": 0
                  },
                  "trends": {
                    "history": [
                      170,
                      170,
                      140,
                      140,
                      170,
                      170,
                      110,
                      170,
                      170,
                      210,
                      210,
                      170
                    ]
                  }
                }
              },
              {
                "keyword": "schema org wordpress",
                "searches": 10,
                "cpc": 0,
                "words_count": 3,
                "trend_1": 10,
                "trend_2": 10,
                "trend_3": 10,
                "trend_4": 10,
                "trend_5": 10,
                "trend_6": 10,
                "trend_7": 10,
                "trend_8": 10,
                "trend_9": 10,
                "trend_10": 10,
                "trend_11": 10,
                "trend_12": 10,
                "trends": [
                  10,
                  10,
                  10,
                  10,
                  10,
                  10,
                  10,
                  10,
                  10,
                  10,
                  10,
                  10
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": {
                    "current": 10
                  },
                  "cpc": {
                    "current": 0
                  },
                  "trends": {
                    "history": [
                      10,
                      10,
                      10,
                      10,
                      10,
                      10,
                      10,
                      10,
                      10,
                      10,
                      10,
                      10
                    ]
                  }
                }
              },
              {
                "keyword": "schema wordpress",
                "searches": 30,
                "cpc": 0,
                "words_count": 2,
                "trend_1": 40,
                "trend_2": 40,
                "trend_3": 20,
                "trend_4": 30,
                "trend_5": 10,
                "trend_6": 40,
                "trend_7": 40,
                "trend_8": 20,
                "trend_9": 30,
                "trend_10": 20,
                "trend_11": 40,
                "trend_12": 10,
                "trends": [
                  40,
                  40,
                  20,
                  30,
                  10,
                  40,
                  40,
                  20,
                  30,
                  20,
                  40,
                  10
                ],
                "serp_params": [
                  "site_links",
                  "video_thumbs"
                ],
                "statistics": {
                  "snippets": {
                    "current": [
                      "site_links",
                      "video_thumbs"
                    ]
                  },
                  "searches": {
                    "current": 30
                  },
                  "cpc": {
                    "current": 0
                  },
                  "trends": {
                    "history": [
                      40,
                      40,
                      20,
                      30,
                      10,
                      40,
                      40,
                      20,
                      30,
                      20,
                      40,
                      10
                    ]
                  }
                }
              },
              {
                "keyword": "dane strukturalne wordpress",
                "searches": 10,
                "cpc": 0,
                "words_count": 3,
                "trend_1": 10,
                "trend_2": 20,
                "trend_3": 10,
                "trend_4": 10,
                "trend_5": 10,
                "trend_6": 10,
                "trend_7": 10,
                "trend_8": 10,
                "trend_9": 10,
                "trend_10": 10,
                "trend_11": 10,
                "trend_12": 10,
                "trends": [
                  10,
                  20,
                  10,
                  10,
                  10,
                  10,
                  10,
                  10,
                  10,
                  10,
                  10,
                  10
                ],
                "serp_params": [
                  "featured_answer",
                  "image_thumbs",
                  "people_also_ask"
                ],
                "statistics": {
                  "snippets": {
                    "current": [
                      "featured_answer",
                      "image_thumbs",
                      "people_also_ask"
                    ]
                  },
                  "searches": {
                    "current": 10
                  },
                  "cpc": {
                    "current": 0
                  },
                  "trends": {
                    "history": [
                      10,
                      20,
                      10,
                      10,
                      10,
                      10,
                      10,
                      10,
                      10,
                      10,
                      10,
                      10
                    ]
                  }
                }
              },
              {
                "keyword": "formularz wieloetapowy",
                "searches": null,
                "cpc": null,
                "words_count": null,
                "trend_1": null,
                "trend_2": null,
                "trend_3": null,
                "trend_4": null,
                "trend_5": null,
                "trend_6": null,
                "trend_7": null,
                "trend_8": null,
                "trend_9": null,
                "trend_10": null,
                "trend_11": null,
                "trend_12": null,
                "trends": [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": [],
                  "cpc": [],
                  "trends": {
                    "history": [
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0
                    ]
                  }
                }
              },
              {
                "keyword": "formularz wieloetapowy wordpress",
                "searches": null,
                "cpc": null,
                "words_count": null,
                "trend_1": null,
                "trend_2": null,
                "trend_3": null,
                "trend_4": null,
                "trend_5": null,
                "trend_6": null,
                "trend_7": null,
                "trend_8": null,
                "trend_9": null,
                "trend_10": null,
                "trend_11": null,
                "trend_12": null,
                "trends": [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": [],
                  "cpc": [],
                  "trends": {
                    "history": [
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0
                    ]
                  }
                }
              },
              {
                "keyword": "formularz krokowy",
                "searches": null,
                "cpc": null,
                "words_count": null,
                "trend_1": null,
                "trend_2": null,
                "trend_3": null,
                "trend_4": null,
                "trend_5": null,
                "trend_6": null,
                "trend_7": null,
                "trend_8": null,
                "trend_9": null,
                "trend_10": null,
                "trend_11": null,
                "trend_12": null,
                "trends": [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": [],
                  "cpc": [],
                  "trends": {
                    "history": [
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0
                    ]
                  }
                }
              },
              {
                "keyword": "multi step form wordpress",
                "searches": 10,
                "cpc": 5.2,
                "words_count": 4,
                "trend_1": 10,
                "trend_2": 10,
                "trend_3": 10,
                "trend_4": 10,
                "trend_5": 10,
                "trend_6": 10,
                "trend_7": 10,
                "trend_8": 10,
                "trend_9": 20,
                "trend_10": 10,
                "trend_11": 10,
                "trend_12": 20,
                "trends": [
                  10,
                  10,
                  10,
                  10,
                  10,
                  10,
                  10,
                  10,
                  20,
                  10,
                  10,
                  20
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": {
                    "current": 10
                  },
                  "cpc": {
                    "current": 5.2
                  },
                  "trends": {
                    "history": [
                      10,
                      10,
                      10,
                      10,
                      10,
                      10,
                      10,
                      10,
                      20,
                      10,
                      10,
                      20
                    ]
                  }
                }
              },
              {
                "keyword": "paginacja woocommerce",
                "searches": null,
                "cpc": null,
                "words_count": null,
                "trend_1": null,
                "trend_2": null,
                "trend_3": null,
                "trend_4": null,
                "trend_5": null,
                "trend_6": null,
                "trend_7": null,
                "trend_8": null,
                "trend_9": null,
                "trend_10": null,
                "trend_11": null,
                "trend_12": null,
                "trends": [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": [],
                  "cpc": [],
                  "trends": {
                    "history": [
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0
                    ]
                  }
                }
              },
              {
                "keyword": "woocommerce paginacja",
                "searches": null,
                "cpc": null,
                "words_count": null,
                "trend_1": null,
                "trend_2": null,
                "trend_3": null,
                "trend_4": null,
                "trend_5": null,
                "trend_6": null,
                "trend_7": null,
                "trend_8": null,
                "trend_9": null,
                "trend_10": null,
                "trend_11": null,
                "trend_12": null,
                "trends": [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": [],
                  "cpc": [],
                  "trends": {
                    "history": [
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0
                    ]
                  }
                }
              },
              {
                "keyword": "paginacja produktów",
                "searches": null,
                "cpc": null,
                "words_count": null,
                "trend_1": null,
                "trend_2": null,
                "trend_3": null,
                "trend_4": null,
                "trend_5": null,
                "trend_6": null,
                "trend_7": null,
                "trend_8": null,
                "trend_9": null,
                "trend_10": null,
                "trend_11": null,
                "trend_12": null,
                "trends": [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": [],
                  "cpc": [],
                  "trends": {
                    "history": [
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0
                    ]
                  }
                }
              },
              {
                "keyword": "infinite scroll woocommerce",
                "searches": 10,
                "cpc": 0,
                "words_count": 3,
                "trend_1": 10,
                "trend_2": 10,
                "trend_3": 10,
                "trend_4": 10,
                "trend_5": 10,
                "trend_6": 10,
                "trend_7": 10,
                "trend_8": 10,
                "trend_9": 10,
                "trend_10": 20,
                "trend_11": 10,
                "trend_12": 10,
                "trends": [
                  10,
                  10,
                  10,
                  10,
                  10,
                  10,
                  10,
                  10,
                  10,
                  20,
                  10,
                  10
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": {
                    "current": 10
                  },
                  "cpc": {
                    "current": 0
                  },
                  "trends": {
                    "history": [
                      10,
                      10,
                      10,
                      10,
                      10,
                      10,
                      10,
                      10,
                      10,
                      20,
                      10,
                      10
                    ]
                  }
                }
              },
              {
                "keyword": "filtry produktów woocommerce",
                "searches": null,
                "cpc": null,
                "words_count": null,
                "trend_1": null,
                "trend_2": null,
                "trend_3": null,
                "trend_4": null,
                "trend_5": null,
                "trend_6": null,
                "trend_7": null,
                "trend_8": null,
                "trend_9": null,
                "trend_10": null,
                "trend_11": null,
                "trend_12": null,
                "trends": [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": [],
                  "cpc": [],
                  "trends": {
                    "history": [
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0
                    ]
                  }
                }
              },
              {
                "keyword": "filtry woocommerce",
                "searches": null,
                "cpc": null,
                "words_count": null,
                "trend_1": null,
                "trend_2": null,
                "trend_3": null,
                "trend_4": null,
                "trend_5": null,
                "trend_6": null,
                "trend_7": null,
                "trend_8": null,
                "trend_9": null,
                "trend_10": null,
                "trend_11": null,
                "trend_12": null,
                "trends": [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": [],
                  "cpc": [],
                  "trends": {
                    "history": [
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0
                    ]
                  }
                }
              },
              {
                "keyword": "filtrowanie produktów woocommerce",
                "searches": null,
                "cpc": null,
                "words_count": null,
                "trend_1": null,
                "trend_2": null,
                "trend_3": null,
                "trend_4": null,
                "trend_5": null,
                "trend_6": null,
                "trend_7": null,
                "trend_8": null,
                "trend_9": null,
                "trend_10": null,
                "trend_11": null,
                "trend_12": null,
                "trends": [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": [],
                  "cpc": [],
                  "trends": {
                    "history": [
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0
                    ]
                  }
                }
              },
              {
                "keyword": "woocommerce filtry produktów",
                "searches": null,
                "cpc": null,
                "words_count": null,
                "trend_1": null,
                "trend_2": null,
                "trend_3": null,
                "trend_4": null,
                "trend_5": null,
                "trend_6": null,
                "trend_7": null,
                "trend_8": null,
                "trend_9": null,
                "trend_10": null,
                "trend_11": null,
                "trend_12": null,
                "trends": [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": [],
                  "cpc": [],
                  "trends": {
                    "history": [
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0
                    ]
                  }
                }
              },
              {
                "keyword": "woocommerce warianty",
                "searches": 10,
                "cpc": 0,
                "words_count": 2,
                "trend_1": 20,
                "trend_2": 30,
                "trend_3": 10,
                "trend_4": 10,
                "trend_5": 10,
                "trend_6": 10,
                "trend_7": 10,
                "trend_8": 10,
                "trend_9": 10,
                "trend_10": 20,
                "trend_11": 20,
                "trend_12": 10,
                "trends": [
                  20,
                  30,
                  10,
                  10,
                  10,
                  10,
                  10,
                  10,
                  10,
                  20,
                  20,
                  10
                ],
                "serp_params": [
                  "video_thumbs"
                ],
                "statistics": {
                  "snippets": {
                    "current": [
                      "video_thumbs"
                    ]
                  },
                  "searches": {
                    "current": 10
                  },
                  "cpc": {
                    "current": 0
                  },
                  "trends": {
                    "history": [
                      20,
                      30,
                      10,
                      10,
                      10,
                      10,
                      10,
                      10,
                      10,
                      20,
                      20,
                      10
                    ]
                  }
                }
              },
              {
                "keyword": "warianty woocommerce",
                "searches": null,
                "cpc": null,
                "words_count": null,
                "trend_1": null,
                "trend_2": null,
                "trend_3": null,
                "trend_4": null,
                "trend_5": null,
                "trend_6": null,
                "trend_7": null,
                "trend_8": null,
                "trend_9": null,
                "trend_10": null,
                "trend_11": null,
                "trend_12": null,
                "trends": [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": [],
                  "cpc": [],
                  "trends": {
                    "history": [
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0
                    ]
                  }
                }
              },
              {
                "keyword": "woocommerce produkt wariantowy",
                "searches": null,
                "cpc": null,
                "words_count": null,
                "trend_1": null,
                "trend_2": null,
                "trend_3": null,
                "trend_4": null,
                "trend_5": null,
                "trend_6": null,
                "trend_7": null,
                "trend_8": null,
                "trend_9": null,
                "trend_10": null,
                "trend_11": null,
                "trend_12": null,
                "trends": [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": [],
                  "cpc": [],
                  "trends": {
                    "history": [
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0
                    ]
                  }
                }
              },
              {
                "keyword": "produkty wariantowe woocommerce",
                "searches": null,
                "cpc": null,
                "words_count": null,
                "trend_1": null,
                "trend_2": null,
                "trend_3": null,
                "trend_4": null,
                "trend_5": null,
                "trend_6": null,
                "trend_7": null,
                "trend_8": null,
                "trend_9": null,
                "trend_10": null,
                "trend_11": null,
                "trend_12": null,
                "trends": [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": [],
                  "cpc": [],
                  "trends": {
                    "history": [
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0
                    ]
                  }
                }
              },
              {
                "keyword": "woocommerce wysyłka",
                "searches": 20,
                "cpc": 2.89,
                "words_count": 2,
                "trend_1": 20,
                "trend_2": 30,
                "trend_3": 30,
                "trend_4": 20,
                "trend_5": 20,
                "trend_6": 20,
                "trend_7": 20,
                "trend_8": 30,
                "trend_9": 20,
                "trend_10": 20,
                "trend_11": 30,
                "trend_12": 30,
                "trends": [
                  20,
                  30,
                  30,
                  20,
                  20,
                  20,
                  20,
                  30,
                  20,
                  20,
                  30,
                  30
                ],
                "serp_params": [
                  "adwords",
                  "image_thumbs"
                ],
                "statistics": {
                  "snippets": {
                    "current": [
                      "adwords",
                      "image_thumbs"
                    ]
                  },
                  "searches": {
                    "current": 20
                  },
                  "cpc": {
                    "current": 2.89
                  },
                  "trends": {
                    "history": [
                      20,
                      30,
                      30,
                      20,
                      20,
                      20,
                      20,
                      30,
                      20,
                      20,
                      30,
                      30
                    ]
                  }
                }
              },
              {
                "keyword": "woocommerce strefy wysyłki",
                "searches": null,
                "cpc": null,
                "words_count": null,
                "trend_1": null,
                "trend_2": null,
                "trend_3": null,
                "trend_4": null,
                "trend_5": null,
                "trend_6": null,
                "trend_7": null,
                "trend_8": null,
                "trend_9": null,
                "trend_10": null,
                "trend_11": null,
                "trend_12": null,
                "trends": [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": [],
                  "cpc": [],
                  "trends": {
                    "history": [
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0
                    ]
                  }
                }
              },
              {
                "keyword": "strefy wysyłki woocommerce",
                "searches": null,
                "cpc": null,
                "words_count": null,
                "trend_1": null,
                "trend_2": null,
                "trend_3": null,
                "trend_4": null,
                "trend_5": null,
                "trend_6": null,
                "trend_7": null,
                "trend_8": null,
                "trend_9": null,
                "trend_10": null,
                "trend_11": null,
                "trend_12": null,
                "trends": [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": [],
                  "cpc": [],
                  "trends": {
                    "history": [
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0
                    ]
                  }
                }
              },
              {
                "keyword": "woocommerce brak metod wysyłki",
                "searches": null,
                "cpc": null,
                "words_count": null,
                "trend_1": null,
                "trend_2": null,
                "trend_3": null,
                "trend_4": null,
                "trend_5": null,
                "trend_6": null,
                "trend_7": null,
                "trend_8": null,
                "trend_9": null,
                "trend_10": null,
                "trend_11": null,
                "trend_12": null,
                "trends": [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": [],
                  "cpc": [],
                  "trends": {
                    "history": [
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0
                    ]
                  }
                }
              },
              {
                "keyword": "woocommerce magazyn",
                "searches": 20,
                "cpc": 4.88,
                "words_count": 2,
                "trend_1": 10,
                "trend_2": 30,
                "trend_3": 20,
                "trend_4": 20,
                "trend_5": 10,
                "trend_6": 20,
                "trend_7": 20,
                "trend_8": 10,
                "trend_9": 20,
                "trend_10": 20,
                "trend_11": 20,
                "trend_12": 20,
                "trends": [
                  10,
                  30,
                  20,
                  20,
                  10,
                  20,
                  20,
                  10,
                  20,
                  20,
                  20,
                  20
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": {
                    "current": 20
                  },
                  "cpc": {
                    "current": 4.88
                  },
                  "trends": {
                    "history": [
                      10,
                      30,
                      20,
                      20,
                      10,
                      20,
                      20,
                      10,
                      20,
                      20,
                      20,
                      20
                    ]
                  }
                }
              },
              {
                "keyword": "woocommerce stany magazynowe",
                "searches": null,
                "cpc": null,
                "words_count": null,
                "trend_1": null,
                "trend_2": null,
                "trend_3": null,
                "trend_4": null,
                "trend_5": null,
                "trend_6": null,
                "trend_7": null,
                "trend_8": null,
                "trend_9": null,
                "trend_10": null,
                "trend_11": null,
                "trend_12": null,
                "trends": [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": [],
                  "cpc": [],
                  "trends": {
                    "history": [
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0
                    ]
                  }
                }
              },
              {
                "keyword": "stany magazynowe woocommerce",
                "searches": null,
                "cpc": null,
                "words_count": null,
                "trend_1": null,
                "trend_2": null,
                "trend_3": null,
                "trend_4": null,
                "trend_5": null,
                "trend_6": null,
                "trend_7": null,
                "trend_8": null,
                "trend_9": null,
                "trend_10": null,
                "trend_11": null,
                "trend_12": null,
                "trends": [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": [],
                  "cpc": [],
                  "trends": {
                    "history": [
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0
                    ]
                  }
                }
              },
              {
                "keyword": "zarządzanie magazynem woocommerce",
                "searches": null,
                "cpc": null,
                "words_count": null,
                "trend_1": null,
                "trend_2": null,
                "trend_3": null,
                "trend_4": null,
                "trend_5": null,
                "trend_6": null,
                "trend_7": null,
                "trend_8": null,
                "trend_9": null,
                "trend_10": null,
                "trend_11": null,
                "trend_12": null,
                "trends": [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": [],
                  "cpc": [],
                  "trends": {
                    "history": [
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0
                    ]
                  }
                }
              },
              {
                "keyword": "woocommerce konto klienta",
                "searches": null,
                "cpc": null,
                "words_count": null,
                "trend_1": null,
                "trend_2": null,
                "trend_3": null,
                "trend_4": null,
                "trend_5": null,
                "trend_6": null,
                "trend_7": null,
                "trend_8": null,
                "trend_9": null,
                "trend_10": null,
                "trend_11": null,
                "trend_12": null,
                "trends": [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": [],
                  "cpc": [],
                  "trends": {
                    "history": [
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0
                    ]
                  }
                }
              },
              {
                "keyword": "woocommerce zakup bez rejestracji",
                "searches": null,
                "cpc": null,
                "words_count": null,
                "trend_1": null,
                "trend_2": null,
                "trend_3": null,
                "trend_4": null,
                "trend_5": null,
                "trend_6": null,
                "trend_7": null,
                "trend_8": null,
                "trend_9": null,
                "trend_10": null,
                "trend_11": null,
                "trend_12": null,
                "trends": [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": [],
                  "cpc": [],
                  "trends": {
                    "history": [
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0
                    ]
                  }
                }
              },
              {
                "keyword": "woocommerce rejestracja klienta",
                "searches": 30,
                "cpc": 0,
                "words_count": 3,
                "trend_1": 30,
                "trend_2": 30,
                "trend_3": 30,
                "trend_4": 20,
                "trend_5": 30,
                "trend_6": 20,
                "trend_7": 30,
                "trend_8": 30,
                "trend_9": 20,
                "trend_10": 30,
                "trend_11": 30,
                "trend_12": 20,
                "trends": [
                  30,
                  30,
                  30,
                  20,
                  30,
                  20,
                  30,
                  30,
                  20,
                  30,
                  30,
                  20
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": {
                    "current": 30
                  },
                  "cpc": {
                    "current": 0
                  },
                  "trends": {
                    "history": [
                      30,
                      30,
                      30,
                      20,
                      30,
                      20,
                      30,
                      30,
                      20,
                      30,
                      30,
                      20
                    ]
                  }
                }
              },
              {
                "keyword": "zakup bez rejestracji",
                "searches": null,
                "cpc": null,
                "words_count": null,
                "trend_1": null,
                "trend_2": null,
                "trend_3": null,
                "trend_4": null,
                "trend_5": null,
                "trend_6": null,
                "trend_7": null,
                "trend_8": null,
                "trend_9": null,
                "trend_10": null,
                "trend_11": null,
                "trend_12": null,
                "trends": [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": [],
                  "cpc": [],
                  "trends": {
                    "history": [
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0
                    ]
                  }
                }
              },
              {
                "keyword": "wyszukiwarka produktów",
                "searches": 70,
                "cpc": 1.42,
                "words_count": 2,
                "trend_1": 90,
                "trend_2": 90,
                "trend_3": 70,
                "trend_4": 70,
                "trend_5": 70,
                "trend_6": 70,
                "trend_7": 50,
                "trend_8": 50,
                "trend_9": 70,
                "trend_10": 70,
                "trend_11": 90,
                "trend_12": 70,
                "trends": [
                  90,
                  90,
                  70,
                  70,
                  70,
                  70,
                  50,
                  50,
                  70,
                  70,
                  90,
                  70
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": {
                    "current": 70
                  },
                  "cpc": {
                    "current": 1.42
                  },
                  "trends": {
                    "history": [
                      90,
                      90,
                      70,
                      70,
                      70,
                      70,
                      50,
                      50,
                      70,
                      70,
                      90,
                      70
                    ]
                  }
                }
              },
              {
                "keyword": "wyszukiwarka woocommerce",
                "searches": null,
                "cpc": null,
                "words_count": null,
                "trend_1": null,
                "trend_2": null,
                "trend_3": null,
                "trend_4": null,
                "trend_5": null,
                "trend_6": null,
                "trend_7": null,
                "trend_8": null,
                "trend_9": null,
                "trend_10": null,
                "trend_11": null,
                "trend_12": null,
                "trends": [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": [],
                  "cpc": [],
                  "trends": {
                    "history": [
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0
                    ]
                  }
                }
              },
              {
                "keyword": "woocommerce wyszukiwarka",
                "searches": null,
                "cpc": null,
                "words_count": null,
                "trend_1": null,
                "trend_2": null,
                "trend_3": null,
                "trend_4": null,
                "trend_5": null,
                "trend_6": null,
                "trend_7": null,
                "trend_8": null,
                "trend_9": null,
                "trend_10": null,
                "trend_11": null,
                "trend_12": null,
                "trends": [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": [],
                  "cpc": [],
                  "trends": {
                    "history": [
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0
                    ]
                  }
                }
              },
              {
                "keyword": "wyszukiwanie produktów woocommerce",
                "searches": null,
                "cpc": null,
                "words_count": null,
                "trend_1": null,
                "trend_2": null,
                "trend_3": null,
                "trend_4": null,
                "trend_5": null,
                "trend_6": null,
                "trend_7": null,
                "trend_8": null,
                "trend_9": null,
                "trend_10": null,
                "trend_11": null,
                "trend_12": null,
                "trends": [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": [],
                  "cpc": [],
                  "trends": {
                    "history": [
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0
                    ]
                  }
                }
              },
              {
                "keyword": "ga4 ecommerce",
                "searches": 110,
                "cpc": 4.63,
                "words_count": 2,
                "trend_1": 140,
                "trend_2": 140,
                "trend_3": 170,
                "trend_4": 140,
                "trend_5": 170,
                "trend_6": 170,
                "trend_7": 70,
                "trend_8": 70,
                "trend_9": 70,
                "trend_10": 110,
                "trend_11": 110,
                "trend_12": 90,
                "trends": [
                  140,
                  140,
                  170,
                  140,
                  170,
                  170,
                  70,
                  70,
                  70,
                  110,
                  110,
                  90
                ],
                "serp_params": [
                  "image_thumbs",
                  "video_thumbs"
                ],
                "statistics": {
                  "snippets": {
                    "current": [
                      "image_thumbs",
                      "video_thumbs"
                    ]
                  },
                  "searches": {
                    "current": 110
                  },
                  "cpc": {
                    "current": 4.63
                  },
                  "trends": {
                    "history": [
                      140,
                      140,
                      170,
                      140,
                      170,
                      170,
                      70,
                      70,
                      70,
                      110,
                      110,
                      90
                    ]
                  }
                }
              },
              {
                "keyword": "google analytics woocommerce",
                "searches": null,
                "cpc": null,
                "words_count": null,
                "trend_1": null,
                "trend_2": null,
                "trend_3": null,
                "trend_4": null,
                "trend_5": null,
                "trend_6": null,
                "trend_7": null,
                "trend_8": null,
                "trend_9": null,
                "trend_10": null,
                "trend_11": null,
                "trend_12": null,
                "trends": [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": [],
                  "cpc": [],
                  "trends": {
                    "history": [
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0
                    ]
                  }
                }
              },
              {
                "keyword": "woocommerce google analytics",
                "searches": null,
                "cpc": null,
                "words_count": null,
                "trend_1": null,
                "trend_2": null,
                "trend_3": null,
                "trend_4": null,
                "trend_5": null,
                "trend_6": null,
                "trend_7": null,
                "trend_8": null,
                "trend_9": null,
                "trend_10": null,
                "trend_11": null,
                "trend_12": null,
                "trends": [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": [],
                  "cpc": [],
                  "trends": {
                    "history": [
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0
                    ]
                  }
                }
              },
              {
                "keyword": "google tag manager woocommerce",
                "searches": 10,
                "cpc": 1.45,
                "words_count": 4,
                "trend_1": 10,
                "trend_2": 10,
                "trend_3": 10,
                "trend_4": 10,
                "trend_5": 30,
                "trend_6": 10,
                "trend_7": 20,
                "trend_8": 10,
                "trend_9": 10,
                "trend_10": 10,
                "trend_11": 20,
                "trend_12": 10,
                "trends": [
                  10,
                  10,
                  10,
                  10,
                  30,
                  10,
                  20,
                  10,
                  10,
                  10,
                  20,
                  10
                ],
                "serp_params": [
                  "image_thumbs",
                  "video_thumbs"
                ],
                "statistics": {
                  "snippets": {
                    "current": [
                      "image_thumbs",
                      "video_thumbs"
                    ]
                  },
                  "searches": {
                    "current": 10
                  },
                  "cpc": {
                    "current": 1.45
                  },
                  "trends": {
                    "history": [
                      10,
                      10,
                      10,
                      10,
                      30,
                      10,
                      20,
                      10,
                      10,
                      10,
                      20,
                      10
                    ]
                  }
                }
              },
              {
                "keyword": "woocommerce rest api",
                "searches": 90,
                "cpc": 0,
                "words_count": 3,
                "trend_1": 90,
                "trend_2": 110,
                "trend_3": 110,
                "trend_4": 90,
                "trend_5": 110,
                "trend_6": 140,
                "trend_7": 110,
                "trend_8": 90,
                "trend_9": 110,
                "trend_10": 90,
                "trend_11": 90,
                "trend_12": 70,
                "trends": [
                  90,
                  110,
                  110,
                  90,
                  110,
                  140,
                  110,
                  90,
                  110,
                  90,
                  90,
                  70
                ],
                "serp_params": [
                  "image_thumbs",
                  "people_also_ask",
                  "video_thumbs"
                ],
                "statistics": {
                  "snippets": {
                    "current": [
                      "image_thumbs",
                      "people_also_ask",
                      "video_thumbs"
                    ]
                  },
                  "searches": {
                    "current": 90
                  },
                  "cpc": {
                    "current": 0
                  },
                  "trends": {
                    "history": [
                      90,
                      110,
                      110,
                      90,
                      110,
                      140,
                      110,
                      90,
                      110,
                      90,
                      90,
                      70
                    ]
                  }
                }
              },
              {
                "keyword": "api woocommerce",
                "searches": 20,
                "cpc": 2.58,
                "words_count": 2,
                "trend_1": 20,
                "trend_2": 30,
                "trend_3": 10,
                "trend_4": 20,
                "trend_5": 10,
                "trend_6": 10,
                "trend_7": 30,
                "trend_8": 20,
                "trend_9": 20,
                "trend_10": 10,
                "trend_11": 20,
                "trend_12": 20,
                "trends": [
                  20,
                  30,
                  10,
                  20,
                  10,
                  10,
                  30,
                  20,
                  20,
                  10,
                  20,
                  20
                ],
                "serp_params": [
                  "video_thumbs"
                ],
                "statistics": {
                  "snippets": {
                    "current": [
                      "video_thumbs"
                    ]
                  },
                  "searches": {
                    "current": 20
                  },
                  "cpc": {
                    "current": 2.58
                  },
                  "trends": {
                    "history": [
                      20,
                      30,
                      10,
                      20,
                      10,
                      10,
                      30,
                      20,
                      20,
                      10,
                      20,
                      20
                    ]
                  }
                }
              },
              {
                "keyword": "woocommerce api",
                "searches": 170,
                "cpc": 10.77,
                "words_count": 2,
                "trend_1": 210,
                "trend_2": 260,
                "trend_3": 210,
                "trend_4": 140,
                "trend_5": 170,
                "trend_6": 260,
                "trend_7": 260,
                "trend_8": 210,
                "trend_9": 170,
                "trend_10": 90,
                "trend_11": 110,
                "trend_12": 70,
                "trends": [
                  210,
                  260,
                  210,
                  140,
                  170,
                  260,
                  260,
                  210,
                  170,
                  90,
                  110,
                  70
                ],
                "serp_params": [
                  "image_thumbs",
                  "people_also_ask",
                  "video_thumbs"
                ],
                "statistics": {
                  "snippets": {
                    "current": [
                      "image_thumbs",
                      "people_also_ask",
                      "video_thumbs"
                    ]
                  },
                  "searches": {
                    "current": 170
                  },
                  "cpc": {
                    "current": 10.77
                  },
                  "trends": {
                    "history": [
                      210,
                      260,
                      210,
                      140,
                      170,
                      260,
                      260,
                      210,
                      170,
                      90,
                      110,
                      70
                    ]
                  }
                }
              },
              {
                "keyword": "integracja woocommerce erp",
                "searches": null,
                "cpc": null,
                "words_count": null,
                "trend_1": null,
                "trend_2": null,
                "trend_3": null,
                "trend_4": null,
                "trend_5": null,
                "trend_6": null,
                "trend_7": null,
                "trend_8": null,
                "trend_9": null,
                "trend_10": null,
                "trend_11": null,
                "trend_12": null,
                "trends": [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": [],
                  "cpc": [],
                  "trends": {
                    "history": [
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0
                    ]
                  }
                }
              },
              {
                "keyword": "webhook woocommerce",
                "searches": null,
                "cpc": null,
                "words_count": null,
                "trend_1": null,
                "trend_2": null,
                "trend_3": null,
                "trend_4": null,
                "trend_5": null,
                "trend_6": null,
                "trend_7": null,
                "trend_8": null,
                "trend_9": null,
                "trend_10": null,
                "trend_11": null,
                "trend_12": null,
                "trends": [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": [],
                  "cpc": [],
                  "trends": {
                    "history": [
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0
                    ]
                  }
                }
              },
              {
                "keyword": "woocommerce webhook",
                "searches": null,
                "cpc": null,
                "words_count": null,
                "trend_1": null,
                "trend_2": null,
                "trend_3": null,
                "trend_4": null,
                "trend_5": null,
                "trend_6": null,
                "trend_7": null,
                "trend_8": null,
                "trend_9": null,
                "trend_10": null,
                "trend_11": null,
                "trend_12": null,
                "trends": [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": [],
                  "cpc": [],
                  "trends": {
                    "history": [
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0
                    ]
                  }
                }
              },
              {
                "keyword": "webhooks woocommerce",
                "searches": 10,
                "cpc": 2.61,
                "words_count": 2,
                "trend_1": 10,
                "trend_2": 10,
                "trend_3": 10,
                "trend_4": 10,
                "trend_5": 10,
                "trend_6": 10,
                "trend_7": 10,
                "trend_8": 10,
                "trend_9": 10,
                "trend_10": 10,
                "trend_11": 10,
                "trend_12": 20,
                "trends": [
                  10,
                  10,
                  10,
                  10,
                  10,
                  10,
                  10,
                  10,
                  10,
                  10,
                  10,
                  20
                ],
                "serp_params": [
                  "image_thumbs",
                  "video_thumbs"
                ],
                "statistics": {
                  "snippets": {
                    "current": [
                      "image_thumbs",
                      "video_thumbs"
                    ]
                  },
                  "searches": {
                    "current": 10
                  },
                  "cpc": {
                    "current": 2.61
                  },
                  "trends": {
                    "history": [
                      10,
                      10,
                      10,
                      10,
                      10,
                      10,
                      10,
                      10,
                      10,
                      10,
                      10,
                      20
                    ]
                  }
                }
              },
              {
                "keyword": "woocommerce webhooks",
                "searches": 20,
                "cpc": 4.22,
                "words_count": 2,
                "trend_1": 20,
                "trend_2": 30,
                "trend_3": 30,
                "trend_4": 20,
                "trend_5": 30,
                "trend_6": 10,
                "trend_7": 30,
                "trend_8": 20,
                "trend_9": 20,
                "trend_10": 20,
                "trend_11": 20,
                "trend_12": 20,
                "trends": [
                  20,
                  30,
                  30,
                  20,
                  30,
                  10,
                  30,
                  20,
                  20,
                  20,
                  20,
                  20
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": {
                    "current": 20
                  },
                  "cpc": {
                    "current": 4.22
                  },
                  "trends": {
                    "history": [
                      20,
                      30,
                      30,
                      20,
                      30,
                      10,
                      30,
                      20,
                      20,
                      20,
                      20,
                      20
                    ]
                  }
                }
              },
              {
                "keyword": "favicon wordpress",
                "searches": 110,
                "cpc": 0.34,
                "words_count": 2,
                "trend_1": 140,
                "trend_2": 110,
                "trend_3": 90,
                "trend_4": 110,
                "trend_5": 110,
                "trend_6": 110,
                "trend_7": 90,
                "trend_8": 140,
                "trend_9": 140,
                "trend_10": 210,
                "trend_11": 110,
                "trend_12": 140,
                "trends": [
                  140,
                  110,
                  90,
                  110,
                  110,
                  110,
                  90,
                  140,
                  140,
                  210,
                  110,
                  140
                ],
                "serp_params": [
                  "people_also_ask",
                  "video_thumbs"
                ],
                "statistics": {
                  "snippets": {
                    "current": [
                      "people_also_ask",
                      "video_thumbs"
                    ]
                  },
                  "searches": {
                    "current": 110
                  },
                  "cpc": {
                    "current": 0.34
                  },
                  "trends": {
                    "history": [
                      140,
                      110,
                      90,
                      110,
                      110,
                      110,
                      90,
                      140,
                      140,
                      210,
                      110,
                      140
                    ]
                  }
                }
              },
              {
                "keyword": "favicon svg",
                "searches": 50,
                "cpc": 0,
                "words_count": 2,
                "trend_1": 50,
                "trend_2": 50,
                "trend_3": 70,
                "trend_4": 50,
                "trend_5": 40,
                "trend_6": 50,
                "trend_7": 40,
                "trend_8": 50,
                "trend_9": 70,
                "trend_10": 40,
                "trend_11": 70,
                "trend_12": 50,
                "trends": [
                  50,
                  50,
                  70,
                  50,
                  40,
                  50,
                  40,
                  50,
                  70,
                  40,
                  70,
                  50
                ],
                "serp_params": [
                  "image_thumbs",
                  "people_also_ask"
                ],
                "statistics": {
                  "snippets": {
                    "current": [
                      "image_thumbs",
                      "people_also_ask"
                    ]
                  },
                  "searches": {
                    "current": 50
                  },
                  "cpc": {
                    "current": 0
                  },
                  "trends": {
                    "history": [
                      50,
                      50,
                      70,
                      50,
                      40,
                      50,
                      40,
                      50,
                      70,
                      40,
                      70,
                      50
                    ]
                  }
                }
              },
              {
                "keyword": "favicon ico",
                "searches": 1000,
                "cpc": 2.67,
                "words_count": 2,
                "trend_1": 880,
                "trend_2": 880,
                "trend_3": 1000,
                "trend_4": 880,
                "trend_5": 1000,
                "trend_6": 1000,
                "trend_7": 480,
                "trend_8": 720,
                "trend_9": 1000,
                "trend_10": 1300,
                "trend_11": 1600,
                "trend_12": 1300,
                "trends": [
                  880,
                  880,
                  1000,
                  880,
                  1000,
                  1000,
                  480,
                  720,
                  1000,
                  1300,
                  1600,
                  1300
                ],
                "serp_params": [
                  "wiki_right"
                ],
                "statistics": {
                  "snippets": {
                    "current": [
                      "wiki_right"
                    ]
                  },
                  "searches": {
                    "current": 1000
                  },
                  "cpc": {
                    "current": 2.67
                  },
                  "trends": {
                    "history": [
                      880,
                      880,
                      1000,
                      880,
                      1000,
                      1000,
                      480,
                      720,
                      1000,
                      1300,
                      1600,
                      1300
                    ]
                  }
                }
              },
              {
                "keyword": "ikona strony wordpress",
                "searches": 10,
                "cpc": 0,
                "words_count": 3,
                "trend_1": 10,
                "trend_2": 10,
                "trend_3": 10,
                "trend_4": 10,
                "trend_5": 10,
                "trend_6": 10,
                "trend_7": 10,
                "trend_8": 10,
                "trend_9": 10,
                "trend_10": 10,
                "trend_11": 10,
                "trend_12": 10,
                "trends": [
                  10,
                  10,
                  10,
                  10,
                  10,
                  10,
                  10,
                  10,
                  10,
                  10,
                  10,
                  10
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": {
                    "current": 10
                  },
                  "cpc": {
                    "current": 0
                  },
                  "trends": {
                    "history": [
                      10,
                      10,
                      10,
                      10,
                      10,
                      10,
                      10,
                      10,
                      10,
                      10,
                      10,
                      10
                    ]
                  }
                }
              },
              {
                "keyword": "grafika open graph",
                "searches": null,
                "cpc": null,
                "words_count": null,
                "trend_1": null,
                "trend_2": null,
                "trend_3": null,
                "trend_4": null,
                "trend_5": null,
                "trend_6": null,
                "trend_7": null,
                "trend_8": null,
                "trend_9": null,
                "trend_10": null,
                "trend_11": null,
                "trend_12": null,
                "trends": [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": [],
                  "cpc": [],
                  "trends": {
                    "history": [
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0
                    ]
                  }
                }
              },
              {
                "keyword": "miniatura linku",
                "searches": null,
                "cpc": null,
                "words_count": null,
                "trend_1": null,
                "trend_2": null,
                "trend_3": null,
                "trend_4": null,
                "trend_5": null,
                "trend_6": null,
                "trend_7": null,
                "trend_8": null,
                "trend_9": null,
                "trend_10": null,
                "trend_11": null,
                "trend_12": null,
                "trends": [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": [],
                  "cpc": [],
                  "trends": {
                    "history": [
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0
                    ]
                  }
                }
              },
              {
                "keyword": "open graph wordpress",
                "searches": 30,
                "cpc": 0,
                "words_count": 3,
                "trend_1": 30,
                "trend_2": 30,
                "trend_3": 40,
                "trend_4": 20,
                "trend_5": 30,
                "trend_6": 40,
                "trend_7": 30,
                "trend_8": 30,
                "trend_9": 30,
                "trend_10": 20,
                "trend_11": 20,
                "trend_12": 20,
                "trends": [
                  30,
                  30,
                  40,
                  20,
                  30,
                  40,
                  30,
                  30,
                  30,
                  20,
                  20,
                  20
                ],
                "serp_params": [
                  "people_also_ask",
                  "video_thumbs"
                ],
                "statistics": {
                  "snippets": {
                    "current": [
                      "people_also_ask",
                      "video_thumbs"
                    ]
                  },
                  "searches": {
                    "current": 30
                  },
                  "cpc": {
                    "current": 0
                  },
                  "trends": {
                    "history": [
                      30,
                      30,
                      40,
                      20,
                      30,
                      40,
                      30,
                      30,
                      30,
                      20,
                      20,
                      20
                    ]
                  }
                }
              },
              {
                "keyword": "open graph",
                "searches": 320,
                "cpc": 1,
                "words_count": 2,
                "trend_1": 320,
                "trend_2": 320,
                "trend_3": 260,
                "trend_4": 320,
                "trend_5": 320,
                "trend_6": 260,
                "trend_7": 260,
                "trend_8": 320,
                "trend_9": 320,
                "trend_10": 390,
                "trend_11": 390,
                "trend_12": 320,
                "trends": [
                  320,
                  320,
                  260,
                  320,
                  320,
                  260,
                  260,
                  320,
                  320,
                  390,
                  390,
                  320
                ],
                "serp_params": [
                  "people_also_ask",
                  "video_thumbs",
                  "wiki_right"
                ],
                "statistics": {
                  "snippets": {
                    "current": [
                      "people_also_ask",
                      "video_thumbs",
                      "wiki_right"
                    ]
                  },
                  "searches": {
                    "current": 320
                  },
                  "cpc": {
                    "current": 1
                  },
                  "trends": {
                    "history": [
                      320,
                      320,
                      260,
                      320,
                      320,
                      260,
                      260,
                      320,
                      320,
                      390,
                      390,
                      320
                    ]
                  }
                }
              },
              {
                "keyword": "woocommerce waluty",
                "searches": null,
                "cpc": null,
                "words_count": null,
                "trend_1": null,
                "trend_2": null,
                "trend_3": null,
                "trend_4": null,
                "trend_5": null,
                "trend_6": null,
                "trend_7": null,
                "trend_8": null,
                "trend_9": null,
                "trend_10": null,
                "trend_11": null,
                "trend_12": null,
                "trends": [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": [],
                  "cpc": [],
                  "trends": {
                    "history": [
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0
                    ]
                  }
                }
              }
            ],
            "pagination": {
              "page_count": 2,
              "current_page": 1,
              "has_next_page": true,
              "has_prev_page": false,
              "count": 127,
              "limit": 100
            }
          }
        },
        {
          "source_file": "native-statistics127-page2.json",
          "source_sha256": "2caefddd068cfdb2a028998816530c3b292eac601939c71cbedd3720d32375ba",
          "response": {
            "summary": {
              "returns": "one row per requested phrase — searches is that phrase's own volume, never a related phrase's",
              "country_id": "1",
              "public_id": "8492f7c0a6c1b88f2da91d9bb893ddcd",
              "keywords_requested": 127,
              "keywords_found": 6,
              "keywords_with_volume": 6,
              "keywords_found_without_data": 0,
              "keywords_not_found": 121,
              "not_found": [
                "staging wordpress",
                "wordpress staging",
                "środowisko testowe wordpress",
                "strona testowa wordpress",
                "wordpress multisite",
                "multisite wordpress",
                "sieć witryn wordpress",
                "wiele stron wordpress",
                "ssl wordpress",
                "wordpress ssl",
                "mixed content wordpress",
                "mieszana zawartość wordpress",
                "err too many redirects",
                "too many redirects wordpress",
                "wordpress za dużo przekierowań",
                "pętla przekierowań wordpress",
                "błąd 500 wordpress",
                "wordpress błąd 500",
                "internal server error wordpress",
                "wordpress internal server error",
                "błąd 403 wordpress",
                "wordpress błąd 403",
                "403 forbidden wordpress",
                "wordpress 403 forbidden",
                "mapa strony xml",
                "mapa witryny xml",
                "sitemap wordpress",
                "sitemap xml",
                "adres kanoniczny",
                "kanoniczny url",
                "rel canonical",
                "canonical wordpress",
                "robots txt wordpress",
                "plik robots txt",
                "robots txt",
                "soft 404",
                "błąd soft 404",
                "soft 404 wordpress",
                "pozorny błąd 404",
                "błąd 404 wordpress",
                "wordpress 404",
                "przekierowanie 301 wordpress",
                "niedziałające linki",
                "dane strukturalne",
                "schema org wordpress",
                "schema wordpress",
                "dane strukturalne wordpress",
                "formularz wieloetapowy",
                "formularz wieloetapowy wordpress",
                "formularz krokowy"
              ],
              "api_progress": {
                "all_keywords": 127,
                "fetched_keywords": 58
              }
            },
            "all_keywords": [
              {
                "keyword": "woocommerce wiele walut",
                "searches": null,
                "cpc": null,
                "words_count": null,
                "trend_1": null,
                "trend_2": null,
                "trend_3": null,
                "trend_4": null,
                "trend_5": null,
                "trend_6": null,
                "trend_7": null,
                "trend_8": null,
                "trend_9": null,
                "trend_10": null,
                "trend_11": null,
                "trend_12": null,
                "trends": [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": [],
                  "cpc": [],
                  "trends": {
                    "history": [
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0
                    ]
                  }
                }
              },
              {
                "keyword": "wiele walut woocommerce",
                "searches": null,
                "cpc": null,
                "words_count": null,
                "trend_1": null,
                "trend_2": null,
                "trend_3": null,
                "trend_4": null,
                "trend_5": null,
                "trend_6": null,
                "trend_7": null,
                "trend_8": null,
                "trend_9": null,
                "trend_10": null,
                "trend_11": null,
                "trend_12": null,
                "trends": [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": [],
                  "cpc": [],
                  "trends": {
                    "history": [
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0
                    ]
                  }
                }
              },
              {
                "keyword": "multicurrency woocommerce",
                "searches": null,
                "cpc": null,
                "words_count": null,
                "trend_1": null,
                "trend_2": null,
                "trend_3": null,
                "trend_4": null,
                "trend_5": null,
                "trend_6": null,
                "trend_7": null,
                "trend_8": null,
                "trend_9": null,
                "trend_10": null,
                "trend_11": null,
                "trend_12": null,
                "trends": [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": [],
                  "cpc": [],
                  "trends": {
                    "history": [
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0
                    ]
                  }
                }
              },
              {
                "keyword": "woocommerce nie wysyła maili",
                "searches": 20,
                "cpc": 0,
                "words_count": 4,
                "trend_1": 20,
                "trend_2": 30,
                "trend_3": 50,
                "trend_4": 40,
                "trend_5": 30,
                "trend_6": 20,
                "trend_7": 20,
                "trend_8": 20,
                "trend_9": 20,
                "trend_10": 30,
                "trend_11": 10,
                "trend_12": 10,
                "trends": [
                  20,
                  30,
                  50,
                  40,
                  30,
                  20,
                  20,
                  20,
                  20,
                  30,
                  10,
                  10
                ],
                "serp_params": [
                  "featured_answer"
                ],
                "statistics": {
                  "snippets": {
                    "current": [
                      "featured_answer"
                    ]
                  },
                  "searches": {
                    "current": 20
                  },
                  "cpc": {
                    "current": 0
                  },
                  "trends": {
                    "history": [
                      20,
                      30,
                      50,
                      40,
                      30,
                      20,
                      20,
                      20,
                      20,
                      30,
                      10,
                      10
                    ]
                  }
                }
              },
              {
                "keyword": "woocommerce wiadomości email",
                "searches": null,
                "cpc": null,
                "words_count": null,
                "trend_1": null,
                "trend_2": null,
                "trend_3": null,
                "trend_4": null,
                "trend_5": null,
                "trend_6": null,
                "trend_7": null,
                "trend_8": null,
                "trend_9": null,
                "trend_10": null,
                "trend_11": null,
                "trend_12": null,
                "trends": [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": [],
                  "cpc": [],
                  "trends": {
                    "history": [
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0
                    ]
                  }
                }
              },
              {
                "keyword": "maile woocommerce",
                "searches": null,
                "cpc": null,
                "words_count": null,
                "trend_1": null,
                "trend_2": null,
                "trend_3": null,
                "trend_4": null,
                "trend_5": null,
                "trend_6": null,
                "trend_7": null,
                "trend_8": null,
                "trend_9": null,
                "trend_10": null,
                "trend_11": null,
                "trend_12": null,
                "trends": [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": [],
                  "cpc": [],
                  "trends": {
                    "history": [
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0
                    ]
                  }
                }
              },
              {
                "keyword": "woocommerce email zamówienia",
                "searches": null,
                "cpc": null,
                "words_count": null,
                "trend_1": null,
                "trend_2": null,
                "trend_3": null,
                "trend_4": null,
                "trend_5": null,
                "trend_6": null,
                "trend_7": null,
                "trend_8": null,
                "trend_9": null,
                "trend_10": null,
                "trend_11": null,
                "trend_12": null,
                "trends": [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": [],
                  "cpc": [],
                  "trends": {
                    "history": [
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0
                    ]
                  }
                }
              },
              {
                "keyword": "lcp wordpress",
                "searches": null,
                "cpc": null,
                "words_count": null,
                "trend_1": null,
                "trend_2": null,
                "trend_3": null,
                "trend_4": null,
                "trend_5": null,
                "trend_6": null,
                "trend_7": null,
                "trend_8": null,
                "trend_9": null,
                "trend_10": null,
                "trend_11": null,
                "trend_12": null,
                "trends": [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": [],
                  "cpc": [],
                  "trends": {
                    "history": [
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0
                    ]
                  }
                }
              },
              {
                "keyword": "largest contentful paint",
                "searches": null,
                "cpc": null,
                "words_count": null,
                "trend_1": null,
                "trend_2": null,
                "trend_3": null,
                "trend_4": null,
                "trend_5": null,
                "trend_6": null,
                "trend_7": null,
                "trend_8": null,
                "trend_9": null,
                "trend_10": null,
                "trend_11": null,
                "trend_12": null,
                "trends": [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": [],
                  "cpc": [],
                  "trends": {
                    "history": [
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0
                    ]
                  }
                }
              },
              {
                "keyword": "optymalizacja lcp",
                "searches": null,
                "cpc": null,
                "words_count": null,
                "trend_1": null,
                "trend_2": null,
                "trend_3": null,
                "trend_4": null,
                "trend_5": null,
                "trend_6": null,
                "trend_7": null,
                "trend_8": null,
                "trend_9": null,
                "trend_10": null,
                "trend_11": null,
                "trend_12": null,
                "trends": [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": [],
                  "cpc": [],
                  "trends": {
                    "history": [
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0
                    ]
                  }
                }
              },
              {
                "keyword": "lcp strony",
                "searches": null,
                "cpc": null,
                "words_count": null,
                "trend_1": null,
                "trend_2": null,
                "trend_3": null,
                "trend_4": null,
                "trend_5": null,
                "trend_6": null,
                "trend_7": null,
                "trend_8": null,
                "trend_9": null,
                "trend_10": null,
                "trend_11": null,
                "trend_12": null,
                "trends": [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": [],
                  "cpc": [],
                  "trends": {
                    "history": [
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0
                    ]
                  }
                }
              },
              {
                "keyword": "inp wordpress",
                "searches": null,
                "cpc": null,
                "words_count": null,
                "trend_1": null,
                "trend_2": null,
                "trend_3": null,
                "trend_4": null,
                "trend_5": null,
                "trend_6": null,
                "trend_7": null,
                "trend_8": null,
                "trend_9": null,
                "trend_10": null,
                "trend_11": null,
                "trend_12": null,
                "trends": [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": [],
                  "cpc": [],
                  "trends": {
                    "history": [
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0
                    ]
                  }
                }
              },
              {
                "keyword": "interaction to next paint",
                "searches": 50,
                "cpc": 0,
                "words_count": 4,
                "trend_1": 40,
                "trend_2": 40,
                "trend_3": 30,
                "trend_4": 30,
                "trend_5": 40,
                "trend_6": 40,
                "trend_7": 20,
                "trend_8": 50,
                "trend_9": 50,
                "trend_10": 70,
                "trend_11": 90,
                "trend_12": 50,
                "trends": [
                  40,
                  40,
                  30,
                  30,
                  40,
                  40,
                  20,
                  50,
                  50,
                  70,
                  90,
                  50
                ],
                "serp_params": [
                  "image_thumbs",
                  "people_also_ask",
                  "video_thumbs"
                ],
                "statistics": {
                  "snippets": {
                    "current": [
                      "image_thumbs",
                      "people_also_ask",
                      "video_thumbs"
                    ]
                  },
                  "searches": {
                    "current": 50
                  },
                  "cpc": {
                    "current": 0
                  },
                  "trends": {
                    "history": [
                      40,
                      40,
                      30,
                      30,
                      40,
                      40,
                      20,
                      50,
                      50,
                      70,
                      90,
                      50
                    ]
                  }
                }
              },
              {
                "keyword": "optymalizacja inp",
                "searches": null,
                "cpc": null,
                "words_count": null,
                "trend_1": null,
                "trend_2": null,
                "trend_3": null,
                "trend_4": null,
                "trend_5": null,
                "trend_6": null,
                "trend_7": null,
                "trend_8": null,
                "trend_9": null,
                "trend_10": null,
                "trend_11": null,
                "trend_12": null,
                "trends": [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": [],
                  "cpc": [],
                  "trends": {
                    "history": [
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0
                    ]
                  }
                }
              },
              {
                "keyword": "inp strony",
                "searches": null,
                "cpc": null,
                "words_count": null,
                "trend_1": null,
                "trend_2": null,
                "trend_3": null,
                "trend_4": null,
                "trend_5": null,
                "trend_6": null,
                "trend_7": null,
                "trend_8": null,
                "trend_9": null,
                "trend_10": null,
                "trend_11": null,
                "trend_12": null,
                "trends": [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": [],
                  "cpc": [],
                  "trends": {
                    "history": [
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0
                    ]
                  }
                }
              },
              {
                "keyword": "cls wordpress",
                "searches": null,
                "cpc": null,
                "words_count": null,
                "trend_1": null,
                "trend_2": null,
                "trend_3": null,
                "trend_4": null,
                "trend_5": null,
                "trend_6": null,
                "trend_7": null,
                "trend_8": null,
                "trend_9": null,
                "trend_10": null,
                "trend_11": null,
                "trend_12": null,
                "trends": [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": [],
                  "cpc": [],
                  "trends": {
                    "history": [
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0
                    ]
                  }
                }
              },
              {
                "keyword": "cumulative layout shift",
                "searches": null,
                "cpc": null,
                "words_count": null,
                "trend_1": null,
                "trend_2": null,
                "trend_3": null,
                "trend_4": null,
                "trend_5": null,
                "trend_6": null,
                "trend_7": null,
                "trend_8": null,
                "trend_9": null,
                "trend_10": null,
                "trend_11": null,
                "trend_12": null,
                "trends": [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": [],
                  "cpc": [],
                  "trends": {
                    "history": [
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0
                    ]
                  }
                }
              },
              {
                "keyword": "optymalizacja cls",
                "searches": null,
                "cpc": null,
                "words_count": null,
                "trend_1": null,
                "trend_2": null,
                "trend_3": null,
                "trend_4": null,
                "trend_5": null,
                "trend_6": null,
                "trend_7": null,
                "trend_8": null,
                "trend_9": null,
                "trend_10": null,
                "trend_11": null,
                "trend_12": null,
                "trends": [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": [],
                  "cpc": [],
                  "trends": {
                    "history": [
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0
                    ]
                  }
                }
              },
              {
                "keyword": "cls strony",
                "searches": null,
                "cpc": null,
                "words_count": null,
                "trend_1": null,
                "trend_2": null,
                "trend_3": null,
                "trend_4": null,
                "trend_5": null,
                "trend_6": null,
                "trend_7": null,
                "trend_8": null,
                "trend_9": null,
                "trend_10": null,
                "trend_11": null,
                "trend_12": null,
                "trends": [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": [],
                  "cpc": [],
                  "trends": {
                    "history": [
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0
                    ]
                  }
                }
              },
              {
                "keyword": "menu restauracji online",
                "searches": null,
                "cpc": null,
                "words_count": null,
                "trend_1": null,
                "trend_2": null,
                "trend_3": null,
                "trend_4": null,
                "trend_5": null,
                "trend_6": null,
                "trend_7": null,
                "trend_8": null,
                "trend_9": null,
                "trend_10": null,
                "trend_11": null,
                "trend_12": null,
                "trends": [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": [],
                  "cpc": [],
                  "trends": {
                    "history": [
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0
                    ]
                  }
                }
              },
              {
                "keyword": "menu online restauracja",
                "searches": null,
                "cpc": null,
                "words_count": null,
                "trend_1": null,
                "trend_2": null,
                "trend_3": null,
                "trend_4": null,
                "trend_5": null,
                "trend_6": null,
                "trend_7": null,
                "trend_8": null,
                "trend_9": null,
                "trend_10": null,
                "trend_11": null,
                "trend_12": null,
                "trends": [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": [],
                  "cpc": [],
                  "trends": {
                    "history": [
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0
                    ]
                  }
                }
              },
              {
                "keyword": "menu qr restauracja",
                "searches": null,
                "cpc": null,
                "words_count": null,
                "trend_1": null,
                "trend_2": null,
                "trend_3": null,
                "trend_4": null,
                "trend_5": null,
                "trend_6": null,
                "trend_7": null,
                "trend_8": null,
                "trend_9": null,
                "trend_10": null,
                "trend_11": null,
                "trend_12": null,
                "trends": [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": [],
                  "cpc": [],
                  "trends": {
                    "history": [
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0
                    ]
                  }
                }
              },
              {
                "keyword": "cyfrowe menu restauracji",
                "searches": null,
                "cpc": null,
                "words_count": null,
                "trend_1": null,
                "trend_2": null,
                "trend_3": null,
                "trend_4": null,
                "trend_5": null,
                "trend_6": null,
                "trend_7": null,
                "trend_8": null,
                "trend_9": null,
                "trend_10": null,
                "trend_11": null,
                "trend_12": null,
                "trends": [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": [],
                  "cpc": [],
                  "trends": {
                    "history": [
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0
                    ]
                  }
                }
              },
              {
                "keyword": "galeria wordpress",
                "searches": 50,
                "cpc": 0,
                "words_count": 2,
                "trend_1": 70,
                "trend_2": 70,
                "trend_3": 90,
                "trend_4": 50,
                "trend_5": 50,
                "trend_6": 40,
                "trend_7": 50,
                "trend_8": 30,
                "trend_9": 50,
                "trend_10": 40,
                "trend_11": 50,
                "trend_12": 70,
                "trends": [
                  70,
                  70,
                  90,
                  50,
                  50,
                  40,
                  50,
                  30,
                  50,
                  40,
                  50,
                  70
                ],
                "serp_params": [
                  "video_thumbs"
                ],
                "statistics": {
                  "snippets": {
                    "current": [
                      "video_thumbs"
                    ]
                  },
                  "searches": {
                    "current": 50
                  },
                  "cpc": {
                    "current": 0
                  },
                  "trends": {
                    "history": [
                      70,
                      70,
                      90,
                      50,
                      50,
                      40,
                      50,
                      30,
                      50,
                      40,
                      50,
                      70
                    ]
                  }
                }
              },
              {
                "keyword": "galeria zdjęć wordpress",
                "searches": 20,
                "cpc": 0,
                "words_count": 3,
                "trend_1": 20,
                "trend_2": 30,
                "trend_3": 20,
                "trend_4": 10,
                "trend_5": 20,
                "trend_6": 20,
                "trend_7": 20,
                "trend_8": 20,
                "trend_9": 10,
                "trend_10": 30,
                "trend_11": 20,
                "trend_12": 20,
                "trends": [
                  20,
                  30,
                  20,
                  10,
                  20,
                  20,
                  20,
                  20,
                  10,
                  30,
                  20,
                  20
                ],
                "serp_params": [
                  "video_thumbs"
                ],
                "statistics": {
                  "snippets": {
                    "current": [
                      "video_thumbs"
                    ]
                  },
                  "searches": {
                    "current": 20
                  },
                  "cpc": {
                    "current": 0
                  },
                  "trends": {
                    "history": [
                      20,
                      30,
                      20,
                      10,
                      20,
                      20,
                      20,
                      20,
                      10,
                      30,
                      20,
                      20
                    ]
                  }
                }
              },
              {
                "keyword": "wordpress galeria zdjęć",
                "searches": 30,
                "cpc": 0,
                "words_count": 3,
                "trend_1": 30,
                "trend_2": 30,
                "trend_3": 30,
                "trend_4": 30,
                "trend_5": 40,
                "trend_6": 20,
                "trend_7": 30,
                "trend_8": 20,
                "trend_9": 30,
                "trend_10": 20,
                "trend_11": 30,
                "trend_12": 10,
                "trends": [
                  30,
                  30,
                  30,
                  30,
                  40,
                  20,
                  30,
                  20,
                  30,
                  20,
                  30,
                  10
                ],
                "serp_params": [
                  "image_thumbs",
                  "video_thumbs"
                ],
                "statistics": {
                  "snippets": {
                    "current": [
                      "image_thumbs",
                      "video_thumbs"
                    ]
                  },
                  "searches": {
                    "current": 30
                  },
                  "cpc": {
                    "current": 0
                  },
                  "trends": {
                    "history": [
                      30,
                      30,
                      30,
                      30,
                      40,
                      20,
                      30,
                      20,
                      30,
                      20,
                      30,
                      10
                    ]
                  }
                }
              },
              {
                "keyword": "galeria na stronie internetowej",
                "searches": 10,
                "cpc": 0,
                "words_count": 3,
                "trend_1": 10,
                "trend_2": 10,
                "trend_3": 10,
                "trend_4": 10,
                "trend_5": 10,
                "trend_6": 10,
                "trend_7": 10,
                "trend_8": 10,
                "trend_9": 10,
                "trend_10": 10,
                "trend_11": 0,
                "trend_12": 0,
                "trends": [
                  10,
                  10,
                  10,
                  10,
                  10,
                  10,
                  10,
                  10,
                  10,
                  10,
                  0,
                  0
                ],
                "serp_params": [
                  "video_thumbs"
                ],
                "statistics": {
                  "snippets": {
                    "current": [
                      "video_thumbs"
                    ]
                  },
                  "searches": {
                    "current": 10
                  },
                  "cpc": {
                    "current": 0
                  },
                  "trends": {
                    "history": [
                      10,
                      10,
                      10,
                      10,
                      10,
                      10,
                      10,
                      10,
                      10,
                      10,
                      0,
                      0
                    ]
                  }
                }
              }
            ],
            "pagination": {
              "page_count": 2,
              "current_page": 2,
              "has_next_page": false,
              "has_prev_page": true,
              "count": 127,
              "limit": 100
            }
          }
        }
      ]
    },
    {
      "id": "pl1-commercial21",
      "request": {
        "country_id": "1",
        "detail_level": "extended",
        "keywords": [
          "cross sells",
          "up selling",
          "cross selling co to",
          "upsell co to",
          "cross up sell",
          "up selling co to",
          "potwierdzenie przyjęcia zamówienia",
          "potwierdzenie zamówienia",
          "testy regresji",
          "test regresji",
          "testy automatyczne",
          "mvp",
          "pim",
          "system pim",
          "e commerce kpi",
          "time and materials",
          "t&m",
          "powiadomienia push",
          "wiadomości push",
          "headless commerce",
          "omnichannel"
        ],
        "limit": 21,
        "page": 1
      },
      "public_id": "6ef5bc9865140aaa93f2da85779e3966",
      "retrieved_observation_date": "2026-10-11",
      "provider_measurement_date": null,
      "snapshots": [
        {
          "source_file": "native-commercial-statistics21.json",
          "source_sha256": "2fdf2055cb41396967d9b2e2e3dc94060cc7b066057d4239fe99c42513086c2e",
          "response": {
            "summary": {
              "returns": "one row per requested phrase — searches is that phrase's own volume, never a related phrase's",
              "country_id": "1",
              "public_id": "6ef5bc9865140aaa93f2da85779e3966",
              "keywords_requested": 21,
              "keywords_found": 19,
              "keywords_with_volume": 19,
              "keywords_found_without_data": 0,
              "keywords_not_found": 2,
              "not_found": [
                "cross up sell",
                "t&m"
              ],
              "api_progress": {
                "all_keywords": 21,
                "fetched_keywords": 19
              }
            },
            "all_keywords": [
              {
                "keyword": "cross sells",
                "searches": 2400,
                "cpc": 0.17,
                "words_count": 2,
                "trend_1": 2900,
                "trend_2": 2400,
                "trend_3": 3600,
                "trend_4": 2400,
                "trend_5": 2400,
                "trend_6": 2400,
                "trend_7": 2400,
                "trend_8": 1900,
                "trend_9": 2400,
                "trend_10": 2900,
                "trend_11": 2900,
                "trend_12": 1900,
                "trends": [
                  2900,
                  2400,
                  3600,
                  2400,
                  2400,
                  2400,
                  2400,
                  1900,
                  2400,
                  2900,
                  2900,
                  1900
                ],
                "serp_params": [
                  "people_also_ask",
                  "wiki_right"
                ],
                "statistics": {
                  "snippets": {
                    "current": [
                      "people_also_ask",
                      "wiki_right"
                    ]
                  },
                  "searches": {
                    "current": 2400
                  },
                  "cpc": {
                    "current": 0.17
                  },
                  "trends": {
                    "history": [
                      2900,
                      2400,
                      3600,
                      2400,
                      2400,
                      2400,
                      2400,
                      1900,
                      2400,
                      2900,
                      2900,
                      1900
                    ]
                  }
                }
              },
              {
                "keyword": "up selling",
                "searches": 2400,
                "cpc": 0.03,
                "words_count": 2,
                "trend_1": 2900,
                "trend_2": 2400,
                "trend_3": 3600,
                "trend_4": 2400,
                "trend_5": 2400,
                "trend_6": 2900,
                "trend_7": 1900,
                "trend_8": 1900,
                "trend_9": 1900,
                "trend_10": 2400,
                "trend_11": 2400,
                "trend_12": 1900,
                "trends": [
                  2900,
                  2400,
                  3600,
                  2400,
                  2400,
                  2900,
                  1900,
                  1900,
                  1900,
                  2400,
                  2400,
                  1900
                ],
                "serp_params": [
                  "wiki_right"
                ],
                "statistics": {
                  "snippets": {
                    "current": [
                      "wiki_right"
                    ]
                  },
                  "searches": {
                    "current": 2400
                  },
                  "cpc": {
                    "current": 0.03
                  },
                  "trends": {
                    "history": [
                      2900,
                      2400,
                      3600,
                      2400,
                      2400,
                      2900,
                      1900,
                      1900,
                      1900,
                      2400,
                      2400,
                      1900
                    ]
                  }
                }
              },
              {
                "keyword": "cross selling co to",
                "searches": 590,
                "cpc": 0.48,
                "words_count": 4,
                "trend_1": 720,
                "trend_2": 590,
                "trend_3": 720,
                "trend_4": 720,
                "trend_5": 590,
                "trend_6": 590,
                "trend_7": 590,
                "trend_8": 480,
                "trend_9": 480,
                "trend_10": 590,
                "trend_11": 590,
                "trend_12": 480,
                "trends": [
                  720,
                  590,
                  720,
                  720,
                  590,
                  590,
                  590,
                  480,
                  480,
                  590,
                  590,
                  480
                ],
                "serp_params": [
                  "people_also_ask",
                  "video_thumbs",
                  "wiki_right"
                ],
                "statistics": {
                  "snippets": {
                    "current": [
                      "people_also_ask",
                      "video_thumbs",
                      "wiki_right"
                    ]
                  },
                  "searches": {
                    "current": 590
                  },
                  "cpc": {
                    "current": 0.48
                  },
                  "trends": {
                    "history": [
                      720,
                      590,
                      720,
                      720,
                      590,
                      590,
                      590,
                      480,
                      480,
                      590,
                      590,
                      480
                    ]
                  }
                }
              },
              {
                "keyword": "upsell co to",
                "searches": 480,
                "cpc": 1.26,
                "words_count": 3,
                "trend_1": 590,
                "trend_2": 480,
                "trend_3": 880,
                "trend_4": 480,
                "trend_5": 480,
                "trend_6": 480,
                "trend_7": 390,
                "trend_8": 390,
                "trend_9": 480,
                "trend_10": 720,
                "trend_11": 390,
                "trend_12": 260,
                "trends": [
                  590,
                  480,
                  880,
                  480,
                  480,
                  480,
                  390,
                  390,
                  480,
                  720,
                  390,
                  260
                ],
                "serp_params": [
                  "people_also_ask",
                  "wiki_right"
                ],
                "statistics": {
                  "snippets": {
                    "current": [
                      "people_also_ask",
                      "wiki_right"
                    ]
                  },
                  "searches": {
                    "current": 480
                  },
                  "cpc": {
                    "current": 1.26
                  },
                  "trends": {
                    "history": [
                      590,
                      480,
                      880,
                      480,
                      480,
                      480,
                      390,
                      390,
                      480,
                      720,
                      390,
                      260
                    ]
                  }
                }
              },
              {
                "keyword": "cross up sell",
                "searches": null,
                "cpc": null,
                "words_count": null,
                "trend_1": null,
                "trend_2": null,
                "trend_3": null,
                "trend_4": null,
                "trend_5": null,
                "trend_6": null,
                "trend_7": null,
                "trend_8": null,
                "trend_9": null,
                "trend_10": null,
                "trend_11": null,
                "trend_12": null,
                "trends": [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": [],
                  "cpc": [],
                  "trends": {
                    "history": [
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0
                    ]
                  }
                }
              },
              {
                "keyword": "up selling co to",
                "searches": 210,
                "cpc": 0.52,
                "words_count": 4,
                "trend_1": 140,
                "trend_2": 170,
                "trend_3": 210,
                "trend_4": 210,
                "trend_5": 260,
                "trend_6": 210,
                "trend_7": 210,
                "trend_8": 260,
                "trend_9": 210,
                "trend_10": 260,
                "trend_11": 210,
                "trend_12": 210,
                "trends": [
                  140,
                  170,
                  210,
                  210,
                  260,
                  210,
                  210,
                  260,
                  210,
                  260,
                  210,
                  210
                ],
                "serp_params": [
                  "wiki_right"
                ],
                "statistics": {
                  "snippets": {
                    "current": [
                      "wiki_right"
                    ]
                  },
                  "searches": {
                    "current": 210
                  },
                  "cpc": {
                    "current": 0.52
                  },
                  "trends": {
                    "history": [
                      140,
                      170,
                      210,
                      210,
                      260,
                      210,
                      210,
                      260,
                      210,
                      260,
                      210,
                      210
                    ]
                  }
                }
              },
              {
                "keyword": "potwierdzenie przyjęcia zamówienia",
                "searches": 390,
                "cpc": 0,
                "words_count": 3,
                "trend_1": 320,
                "trend_2": 260,
                "trend_3": 260,
                "trend_4": 260,
                "trend_5": 140,
                "trend_6": 1900,
                "trend_7": 30,
                "trend_8": 20,
                "trend_9": 170,
                "trend_10": 260,
                "trend_11": 320,
                "trend_12": 320,
                "trends": [
                  320,
                  260,
                  260,
                  260,
                  140,
                  1900,
                  30,
                  20,
                  170,
                  260,
                  320,
                  320
                ],
                "serp_params": [
                  "image_thumbs",
                  "people_also_ask"
                ],
                "statistics": {
                  "snippets": {
                    "current": [
                      "image_thumbs",
                      "people_also_ask"
                    ]
                  },
                  "searches": {
                    "current": 390
                  },
                  "cpc": {
                    "current": 0
                  },
                  "trends": {
                    "history": [
                      320,
                      260,
                      260,
                      260,
                      140,
                      1900,
                      30,
                      20,
                      170,
                      260,
                      320,
                      320
                    ]
                  }
                }
              },
              {
                "keyword": "potwierdzenie zamówienia",
                "searches": 260,
                "cpc": 0,
                "words_count": 2,
                "trend_1": 320,
                "trend_2": 260,
                "trend_3": 210,
                "trend_4": 260,
                "trend_5": 320,
                "trend_6": 320,
                "trend_7": 210,
                "trend_8": 260,
                "trend_9": 210,
                "trend_10": 210,
                "trend_11": 210,
                "trend_12": 210,
                "trends": [
                  320,
                  260,
                  210,
                  260,
                  320,
                  320,
                  210,
                  260,
                  210,
                  210,
                  210,
                  210
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": {
                    "current": 260
                  },
                  "cpc": {
                    "current": 0
                  },
                  "trends": {
                    "history": [
                      320,
                      260,
                      210,
                      260,
                      320,
                      320,
                      210,
                      260,
                      210,
                      210,
                      210,
                      210
                    ]
                  }
                }
              },
              {
                "keyword": "testy regresji",
                "searches": 0,
                "cpc": 0,
                "words_count": 2,
                "trend_1": 0,
                "trend_2": 0,
                "trend_3": 0,
                "trend_4": 0,
                "trend_5": 0,
                "trend_6": 0,
                "trend_7": 0,
                "trend_8": 0,
                "trend_9": 0,
                "trend_10": 0,
                "trend_11": 0,
                "trend_12": 0,
                "trends": [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "serp_params": [
                  "people_also_ask",
                  "wiki_right"
                ],
                "statistics": {
                  "snippets": {
                    "current": [
                      "people_also_ask",
                      "wiki_right"
                    ]
                  },
                  "searches": {
                    "current": 0
                  },
                  "cpc": {
                    "current": 0
                  },
                  "trends": {
                    "history": [
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0
                    ]
                  }
                }
              },
              {
                "keyword": "test regresji",
                "searches": 1300,
                "cpc": 2.75,
                "words_count": 2,
                "trend_1": 1600,
                "trend_2": 1300,
                "trend_3": 1300,
                "trend_4": 1000,
                "trend_5": 1300,
                "trend_6": 1000,
                "trend_7": 1300,
                "trend_8": 1000,
                "trend_9": 1000,
                "trend_10": 1300,
                "trend_11": 1300,
                "trend_12": 1000,
                "trends": [
                  1600,
                  1300,
                  1300,
                  1000,
                  1300,
                  1000,
                  1300,
                  1000,
                  1000,
                  1300,
                  1300,
                  1000
                ],
                "serp_params": [
                  "people_also_ask",
                  "video_thumbs",
                  "wiki_right"
                ],
                "statistics": {
                  "snippets": {
                    "current": [
                      "people_also_ask",
                      "video_thumbs",
                      "wiki_right"
                    ]
                  },
                  "searches": {
                    "current": 1300
                  },
                  "cpc": {
                    "current": 2.75
                  },
                  "trends": {
                    "history": [
                      1600,
                      1300,
                      1300,
                      1000,
                      1300,
                      1000,
                      1300,
                      1000,
                      1000,
                      1300,
                      1300,
                      1000
                    ]
                  }
                }
              },
              {
                "keyword": "testy automatyczne",
                "searches": 170,
                "cpc": 4.77,
                "words_count": 2,
                "trend_1": 140,
                "trend_2": 210,
                "trend_3": 210,
                "trend_4": 170,
                "trend_5": 140,
                "trend_6": 170,
                "trend_7": 140,
                "trend_8": 210,
                "trend_9": 210,
                "trend_10": 210,
                "trend_11": 110,
                "trend_12": 170,
                "trends": [
                  140,
                  210,
                  210,
                  170,
                  140,
                  170,
                  140,
                  210,
                  210,
                  210,
                  110,
                  170
                ],
                "serp_params": [
                  "people_also_ask",
                  "video_thumbs"
                ],
                "statistics": {
                  "snippets": {
                    "current": [
                      "people_also_ask",
                      "video_thumbs"
                    ]
                  },
                  "searches": {
                    "current": 170
                  },
                  "cpc": {
                    "current": 4.77
                  },
                  "trends": {
                    "history": [
                      140,
                      210,
                      210,
                      170,
                      140,
                      170,
                      140,
                      210,
                      210,
                      210,
                      110,
                      170
                    ]
                  }
                }
              },
              {
                "keyword": "mvp",
                "searches": 9900,
                "cpc": 1.58,
                "words_count": 1,
                "trend_1": 9900,
                "trend_2": 8100,
                "trend_3": 8100,
                "trend_4": 8100,
                "trend_5": 8100,
                "trend_6": 9900,
                "trend_7": 8100,
                "trend_8": 9900,
                "trend_9": 8100,
                "trend_10": 9900,
                "trend_11": 9900,
                "trend_12": 9900,
                "trends": [
                  9900,
                  8100,
                  8100,
                  8100,
                  8100,
                  9900,
                  8100,
                  9900,
                  8100,
                  9900,
                  9900,
                  9900
                ],
                "serp_params": [
                  "video_thumbs",
                  "wiki_right"
                ],
                "statistics": {
                  "snippets": {
                    "current": [
                      "video_thumbs",
                      "wiki_right"
                    ]
                  },
                  "searches": {
                    "current": 9900
                  },
                  "cpc": {
                    "current": 1.58
                  },
                  "trends": {
                    "history": [
                      9900,
                      8100,
                      8100,
                      8100,
                      8100,
                      9900,
                      8100,
                      9900,
                      8100,
                      9900,
                      9900,
                      9900
                    ]
                  }
                }
              },
              {
                "keyword": "pim",
                "searches": 6600,
                "cpc": 8.59,
                "words_count": 1,
                "trend_1": 6600,
                "trend_2": 6600,
                "trend_3": 5400,
                "trend_4": 5400,
                "trend_5": 6600,
                "trend_6": 6600,
                "trend_7": 6600,
                "trend_8": 6600,
                "trend_9": 6600,
                "trend_10": 6600,
                "trend_11": 6600,
                "trend_12": 5400,
                "trends": [
                  6600,
                  6600,
                  5400,
                  5400,
                  6600,
                  6600,
                  6600,
                  6600,
                  6600,
                  6600,
                  6600,
                  5400
                ],
                "serp_params": [
                  "adwords",
                  "map",
                  "yellow_pages"
                ],
                "statistics": {
                  "snippets": {
                    "current": [
                      "adwords",
                      "map",
                      "yellow_pages"
                    ]
                  },
                  "searches": {
                    "current": 6600
                  },
                  "cpc": {
                    "current": 8.59
                  },
                  "trends": {
                    "history": [
                      6600,
                      6600,
                      5400,
                      5400,
                      6600,
                      6600,
                      6600,
                      6600,
                      6600,
                      6600,
                      6600,
                      5400
                    ]
                  }
                }
              },
              {
                "keyword": "system pim",
                "searches": 170,
                "cpc": 9.85,
                "words_count": 2,
                "trend_1": 110,
                "trend_2": 110,
                "trend_3": 140,
                "trend_4": 210,
                "trend_5": 140,
                "trend_6": 140,
                "trend_7": 90,
                "trend_8": 170,
                "trend_9": 210,
                "trend_10": 390,
                "trend_11": 260,
                "trend_12": 170,
                "trends": [
                  110,
                  110,
                  140,
                  210,
                  140,
                  140,
                  90,
                  170,
                  210,
                  390,
                  260,
                  170
                ],
                "serp_params": [
                  "adwords",
                  "image_thumbs"
                ],
                "statistics": {
                  "snippets": {
                    "current": [
                      "adwords",
                      "image_thumbs"
                    ]
                  },
                  "searches": {
                    "current": 170
                  },
                  "cpc": {
                    "current": 9.85
                  },
                  "trends": {
                    "history": [
                      110,
                      110,
                      140,
                      210,
                      140,
                      140,
                      90,
                      170,
                      210,
                      390,
                      260,
                      170
                    ]
                  }
                }
              },
              {
                "keyword": "e commerce kpi",
                "searches": 170,
                "cpc": 4.37,
                "words_count": 3,
                "trend_1": 90,
                "trend_2": 70,
                "trend_3": 70,
                "trend_4": 40,
                "trend_5": 50,
                "trend_6": 70,
                "trend_7": 70,
                "trend_8": 40,
                "trend_9": 50,
                "trend_10": 30,
                "trend_11": 20,
                "trend_12": 30,
                "trends": [
                  90,
                  70,
                  70,
                  40,
                  50,
                  70,
                  70,
                  40,
                  50,
                  30,
                  20,
                  30
                ],
                "serp_params": [
                  "featured_answer"
                ],
                "statistics": {
                  "snippets": {
                    "current": [
                      "featured_answer"
                    ]
                  },
                  "searches": {
                    "current": 170
                  },
                  "cpc": {
                    "current": 4.37
                  },
                  "trends": {
                    "history": [
                      90,
                      70,
                      70,
                      40,
                      50,
                      70,
                      70,
                      40,
                      50,
                      30,
                      20,
                      30
                    ]
                  }
                }
              },
              {
                "keyword": "time and materials",
                "searches": 390,
                "cpc": 0.35,
                "words_count": 3,
                "trend_1": 390,
                "trend_2": 390,
                "trend_3": 390,
                "trend_4": 390,
                "trend_5": 320,
                "trend_6": 320,
                "trend_7": 320,
                "trend_8": 320,
                "trend_9": 320,
                "trend_10": 390,
                "trend_11": 390,
                "trend_12": 320,
                "trends": [
                  390,
                  390,
                  390,
                  390,
                  320,
                  320,
                  320,
                  320,
                  320,
                  390,
                  390,
                  320
                ],
                "serp_params": [
                  "direct_answer",
                  "people_also_ask",
                  "wiki_right"
                ],
                "statistics": {
                  "snippets": {
                    "current": [
                      "direct_answer",
                      "people_also_ask",
                      "wiki_right"
                    ]
                  },
                  "searches": {
                    "current": 390
                  },
                  "cpc": {
                    "current": 0.35
                  },
                  "trends": {
                    "history": [
                      390,
                      390,
                      390,
                      390,
                      320,
                      320,
                      320,
                      320,
                      320,
                      390,
                      390,
                      320
                    ]
                  }
                }
              },
              {
                "keyword": "t&m",
                "searches": null,
                "cpc": null,
                "words_count": null,
                "trend_1": null,
                "trend_2": null,
                "trend_3": null,
                "trend_4": null,
                "trend_5": null,
                "trend_6": null,
                "trend_7": null,
                "trend_8": null,
                "trend_9": null,
                "trend_10": null,
                "trend_11": null,
                "trend_12": null,
                "trends": [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "serp_params": [],
                "statistics": {
                  "snippets": {
                    "current": []
                  },
                  "searches": [],
                  "cpc": [],
                  "trends": {
                    "history": [
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0
                    ]
                  }
                }
              },
              {
                "keyword": "powiadomienia push",
                "searches": 1600,
                "cpc": 10.37,
                "words_count": 2,
                "trend_1": 1600,
                "trend_2": 1600,
                "trend_3": 1300,
                "trend_4": 1300,
                "trend_5": 1900,
                "trend_6": 1900,
                "trend_7": 1900,
                "trend_8": 1900,
                "trend_9": 1900,
                "trend_10": 1600,
                "trend_11": 1600,
                "trend_12": 1600,
                "trends": [
                  1600,
                  1600,
                  1300,
                  1300,
                  1900,
                  1900,
                  1900,
                  1900,
                  1900,
                  1600,
                  1600,
                  1600
                ],
                "serp_params": [
                  "people_also_ask",
                  "video_thumbs"
                ],
                "statistics": {
                  "snippets": {
                    "current": [
                      "people_also_ask",
                      "video_thumbs"
                    ]
                  },
                  "searches": {
                    "current": 1600
                  },
                  "cpc": {
                    "current": 10.37
                  },
                  "trends": {
                    "history": [
                      1600,
                      1600,
                      1300,
                      1300,
                      1900,
                      1900,
                      1900,
                      1900,
                      1900,
                      1600,
                      1600,
                      1600
                    ]
                  }
                }
              },
              {
                "keyword": "wiadomości push",
                "searches": 260,
                "cpc": 0,
                "words_count": 2,
                "trend_1": 260,
                "trend_2": 260,
                "trend_3": 260,
                "trend_4": 210,
                "trend_5": 210,
                "trend_6": 210,
                "trend_7": 260,
                "trend_8": 210,
                "trend_9": 260,
                "trend_10": 210,
                "trend_11": 210,
                "trend_12": 260,
                "trends": [
                  260,
                  260,
                  260,
                  210,
                  210,
                  210,
                  260,
                  210,
                  260,
                  210,
                  210,
                  260
                ],
                "serp_params": [
                  "adwords",
                  "people_also_ask"
                ],
                "statistics": {
                  "snippets": {
                    "current": [
                      "adwords",
                      "people_also_ask"
                    ]
                  },
                  "searches": {
                    "current": 260
                  },
                  "cpc": {
                    "current": 0
                  },
                  "trends": {
                    "history": [
                      260,
                      260,
                      260,
                      210,
                      210,
                      210,
                      260,
                      210,
                      260,
                      210,
                      210,
                      260
                    ]
                  }
                }
              },
              {
                "keyword": "headless commerce",
                "searches": 170,
                "cpc": 10.04,
                "words_count": 2,
                "trend_1": 110,
                "trend_2": 70,
                "trend_3": 140,
                "trend_4": 70,
                "trend_5": 110,
                "trend_6": 70,
                "trend_7": 50,
                "trend_8": 50,
                "trend_9": 140,
                "trend_10": 110,
                "trend_11": 90,
                "trend_12": 70,
                "trends": [
                  110,
                  70,
                  140,
                  70,
                  110,
                  70,
                  50,
                  50,
                  140,
                  110,
                  90,
                  70
                ],
                "serp_params": [
                  "adwords",
                  "featured_answer",
                  "video_thumbs",
                  "wiki_right"
                ],
                "statistics": {
                  "snippets": {
                    "current": [
                      "adwords",
                      "featured_answer",
                      "video_thumbs",
                      "wiki_right"
                    ]
                  },
                  "searches": {
                    "current": 170
                  },
                  "cpc": {
                    "current": 10.04
                  },
                  "trends": {
                    "history": [
                      110,
                      70,
                      140,
                      70,
                      110,
                      70,
                      50,
                      50,
                      140,
                      110,
                      90,
                      70
                    ]
                  }
                }
              },
              {
                "keyword": "omnichannel",
                "searches": 1900,
                "cpc": 6.03,
                "words_count": 1,
                "trend_1": 2400,
                "trend_2": 1600,
                "trend_3": 1300,
                "trend_4": 1900,
                "trend_5": 2400,
                "trend_6": 2400,
                "trend_7": 1900,
                "trend_8": 2400,
                "trend_9": 1900,
                "trend_10": 2400,
                "trend_11": 2400,
                "trend_12": 2400,
                "trends": [
                  2400,
                  1600,
                  1300,
                  1900,
                  2400,
                  2400,
                  1900,
                  2400,
                  1900,
                  2400,
                  2400,
                  2400
                ],
                "serp_params": [
                  "people_also_ask",
                  "wiki_right"
                ],
                "statistics": {
                  "snippets": {
                    "current": [
                      "people_also_ask",
                      "wiki_right"
                    ]
                  },
                  "searches": {
                    "current": 1900
                  },
                  "cpc": {
                    "current": 6.03
                  },
                  "trends": {
                    "history": [
                      2400,
                      1600,
                      1300,
                      1900,
                      2400,
                      2400,
                      1900,
                      2400,
                      1900,
                      2400,
                      2400,
                      2400
                    ]
                  }
                }
              }
            ],
            "pagination": {
              "page_count": 1,
              "current_page": 1,
              "has_next_page": false,
              "has_prev_page": false,
              "count": 21,
              "limit": 21
            }
          }
        }
      ]
    }
  ],
  "families": {
    "multisite": {
      "keyword": "wordpress multisite",
      "report_id": "pl1-tech127",
      "group": "web",
      "angle": "WordPress Multisite czy osobne instalacje: kto odpowiada za utrzymanie kilku witryn?",
      "closest_existing_post_ids": [
        5716,
        4936
      ],
      "distinctness_reason": "Kilka zarządzanych witryn i aktualizacji; nie kilka języków jednej strony.",
      "rank": 1,
      "resources": [
        {
          "title": "WordPress Multisite: wspólna instalacja i osobne dane witryn",
          "url": "https://developer.wordpress.org/advanced-administration/multisite/"
        },
        {
          "title": "WordPress: tworzenie sieci i decyzje przed uruchomieniem",
          "url": "https://developer.wordpress.org/advanced-administration/multisite/create-network/"
        }
      ],
      "source_ids": [
        "stat_multisite_1",
        "stat_multisite_2"
      ]
    },
    "https": {
      "keyword": "ssl wordpress",
      "report_id": "pl1-tech127",
      "group": "web",
      "angle": "SSL w WordPressie po migracji: certyfikat, mieszana zawartość i odpowiedzialność za naprawę",
      "closest_existing_post_ids": [
        5657,
        5732
      ],
      "distinctness_reason": "Źródła ostrzeżeń HTTPS po przeniesieniu; nie ogólne bezpieczeństwo ani wybór hostingu.",
      "rank": 2,
      "resources": [
        {
          "title": "WordPress HTTPS: aktualna konfiguracja; pomiń historyczne przykłady WP1.5 i2.8",
          "url": "https://developer.wordpress.org/advanced-administration/security/https/"
        },
        {
          "title": "MDN: mieszana zawartość HTTPS i zasady blokowania zasobów",
          "url": "https://developer.mozilla.org/en-US/docs/Web/Security/Defenses/Mixed_content"
        }
      ],
      "source_ids": [
        "stat_https_1",
        "stat_https_2"
      ]
    },
    "sitemap": {
      "keyword": "sitemap xml",
      "report_id": "pl1-tech127",
      "group": "web",
      "angle": "Sitemap XML po zmianach strony: jak sprawdzić adresy i zgłosić aktualną mapę",
      "closest_existing_post_ids": [
        5609,
        4998
      ],
      "distinctness_reason": "Test aktualności i dostępności mapy; nie ogólny poradnik SEO lub migracji.",
      "rank": 3,
      "resources": [
        {
          "title": "Google Search: budowa i zgłaszanie mapy witryny",
          "url": "https://developers.google.com/search/docs/crawling-indexing/sitemaps/build-sitemap"
        },
        {
          "title": "Google Search: możliwości i ograniczenia mapy witryny",
          "url": "https://developers.google.com/search/docs/crawling-indexing/sitemaps/overview"
        }
      ],
      "source_ids": [
        "stat_sitemap_1",
        "stat_sitemap_2"
      ]
    },
    "robots": {
      "keyword": "robots txt",
      "report_id": "pl1-tech127",
      "group": "web",
      "angle": "Robots.txt a noindex: jak ustalić dostęp do strony i uniknąć sprzecznych ustawień",
      "closest_existing_post_ids": [
        5704,
        5609
      ],
      "distinctness_reason": "Rozróżnienie crawl/index oraz odczyt konkretnych reguł, nie pełne SEO ani trening AI.",
      "rank": 4,
      "resources": [
        {
          "title": "Google Search: robots.txt i kontrola indeksowania",
          "url": "https://developers.google.com/search/docs/crawling-indexing/robots/intro"
        },
        {
          "title": "Google Search: noindex oraz konieczny dostęp robota",
          "url": "https://developers.google.com/search/docs/crawling-indexing/block-indexing"
        }
      ],
      "source_ids": [
        "stat_robots_1",
        "stat_robots_2"
      ]
    },
    "structured_data": {
      "keyword": "dane strukturalne",
      "report_id": "pl1-tech127",
      "group": "web",
      "angle": "Dane strukturalne na stronie firmy: jak porównać znaczniki z widoczną ofertą",
      "closest_existing_post_ids": [
        5609,
        5704
      ],
      "distinctness_reason": "Zgodność oznaczeń z publiczną treścią i diagnostyka, bez gwarancji rich results lub cytowania.",
      "rank": 5,
      "resources": [
        {
          "title": "Google Search: jak działają dane strukturalne",
          "url": "https://developers.google.com/search/docs/appearance/structured-data/intro-structured-data"
        },
        {
          "title": "Google Search: zasady jakości danych strukturalnych",
          "url": "https://developers.google.com/search/docs/appearance/structured-data/sd-policies"
        }
      ],
      "source_ids": [
        "stat_structured_data_1",
        "stat_structured_data_2"
      ]
    },
    "woo_api": {
      "keyword": "woocommerce api",
      "report_id": "pl1-tech127",
      "group": "shop",
      "angle": "WooCommerce API przed integracją: jakie dane, uprawnienia i właściciela dostępu ustalić",
      "closest_existing_post_ids": [
        5659,
        5738
      ],
      "distinctness_reason": "Granice dostępu i odpowiedzialność integracji; nie kolejna lista kurierów albo import CSV.",
      "rank": 6,
      "resources": [
        {
          "title": "WooCommerce: dostępne API i ich odrębne zastosowania",
          "url": "https://developer.woocommerce.com/docs/apis/"
        },
        {
          "title": "WooCommerce REST: klucze, role i odwołanie dostępu",
          "url": "https://developer.woocommerce.com/docs/apis/rest-api/authentication"
        }
      ],
      "source_ids": [
        "stat_woo_api_1",
        "stat_woo_api_2"
      ]
    },
    "favicon": {
      "keyword": "favicon ico",
      "report_id": "pl1-tech127",
      "group": "web",
      "angle": "Favicon ICO i ikona witryny: jakie pliki przygotować i jak sprawdzić wdrożenie",
      "closest_existing_post_ids": [
        5409,
        5051
      ],
      "distinctness_reason": "Mała ikona w przeglądarce i wyszukiwarce; nie kolejny ogólny pakiet plików logo.",
      "rank": 7,
      "resources": [
        {
          "title": "WordPress: wbudowana ikona witryny zamiast ręcznego pliku ICO",
          "url": "https://wordpress.org/documentation/article/create-a-favicon/"
        },
        {
          "title": "Google Search: formaty, dostępność i brak gwarancji favicon",
          "url": "https://developers.google.com/search/docs/appearance/favicon-in-search"
        }
      ],
      "source_ids": [
        "stat_favicon_1",
        "stat_favicon_2"
      ]
    },
    "open_graph": {
      "keyword": "open graph",
      "report_id": "pl1-tech127",
      "group": "web",
      "angle": "Open Graph na stronie firmy: jak zaplanować miniaturę linku i sprawdzić metadane",
      "closest_existing_post_ids": [
        5402,
        2470
      ],
      "distinctness_reason": "Kadr i znaczniki udostępnianego linku; nie ogólny key visual lub strategia marki.",
      "rank": 8,
      "resources": [
        {
          "title": "Open Graph: cztery podstawowe znaczniki i właściwości obrazu",
          "url": "https://ogp.me/"
        },
        {
          "title": "MDN: metadane dokumentu HTML",
          "url": "https://developer.mozilla.org/en-US/docs/Web/HTML/Reference/Elements/meta"
        }
      ],
      "source_ids": [
        "stat_open_graph_1",
        "stat_open_graph_2"
      ]
    },
    "linked_products": {
      "keyword": "cross selling co to",
      "report_id": "pl1-commercial21",
      "group": "shop",
      "angle": "Cross-sell i up-sell w WooCommerce: gdzie pokazać uzupełnienie, a gdzie alternatywę",
      "closest_existing_post_ids": [
        2860,
        4876,
        3352
      ],
      "distinctness_reason": "Dobór i testowanie powiązanych produktów; nie ponowna lista funkcji sklepu ani ogólny koszyk.",
      "rank": 9,
      "resources": [
        {
          "title": "WooCommerce: powiązane produkty, up-sell i cross-sell",
          "url": "https://woocommerce.com/document/related-products-up-sells-and-cross-sells/"
        },
        {
          "title": "WooCommerce: kolekcje produktów i wybór miejsca rekomendacji",
          "url": "https://woocommerce.com/document/woocommerce-store-editing/customizing-shop-page-catalog/product-collection-block/"
        }
      ],
      "source_ids": [
        "stat_linked_products_1",
        "stat_linked_products_2"
      ]
    },
    "order_confirmation": {
      "keyword": "potwierdzenie zamówienia",
      "report_id": "pl1-commercial21",
      "group": "shop",
      "angle": "Potwierdzenie zamówienia w WooCommerce: co pokazać po zakupie i w wiadomości klienta",
      "closest_existing_post_ids": [
        5710,
        5564
      ],
      "distinctness_reason": "Treść i sekwencja komunikatów zamówienia/płatności/realizacji; nie ogólna diagnoza poczty WordPressa.",
      "rank": 10,
      "resources": [
        {
          "title": "WooCommerce: statusy zamówień, płatność i realizacja",
          "url": "https://woocommerce.com/document/managing-orders/order-statuses/"
        },
        {
          "title": "WooCommerce: zdarzenia wysyłki maili i diagnoza",
          "url": "https://woocommerce.com/document/email-faq/"
        }
      ],
      "source_ids": [
        "stat_order_confirmation_1",
        "stat_order_confirmation_2"
      ]
    },
    "regression": {
      "keyword": "testy automatyczne",
      "report_id": "pl1-commercial21",
      "group": "shop",
      "angle": "Testy automatyczne sklepu WooCommerce: co sprawdzać po zmianie checkoutu lub płatności",
      "closest_existing_post_ids": [
        3774,
        5732,
        5742
      ],
      "distinctness_reason": "Powtarzalne scenariusze po zmianie działającego sklepu; nie pierwszy odbiór całej strony ani backup.",
      "rank": 11,
      "resources": [
        {
          "title": "WooCommerce: testy zamówień i skutki uboczne testu",
          "url": "https://woocommerce.com/document/managing-orders/testing-orders/"
        },
        {
          "title": "WordPress: oddzielne środowisko rozwojowe",
          "url": "https://developer.wordpress.org/advanced-administration/before-install/development/"
        }
      ],
      "source_ids": [
        "stat_regression_1",
        "stat_regression_2"
      ]
    },
    "pim": {
      "keyword": "system pim",
      "report_id": "pl1-commercial21",
      "group": "shop",
      "angle": "PIM a sklep internetowy: kto utrzymuje dane produktów i co ustalić przed integracją",
      "closest_existing_post_ids": [
        5738,
        5659
      ],
      "distinctness_reason": "Stała odpowiedzialność za źródło danych, pola, języki i aktualizacje; nie jednorazowy import CSV ani ranking systemów.",
      "rank": 12,
      "resources": [
        {
          "title": "Akeneo: dane produktu, zakres języka i kanału; nie ranking i nie dowód partnerstwa",
          "url": "https://api.akeneo.com/concepts/products.html"
        },
        {
          "title": "WooCommerce: typy produktów i ich dane",
          "url": "https://woocommerce.com/document/managing-products/"
        }
      ],
      "source_ids": [
        "stat_pim_1",
        "stat_pim_2"
      ]
    },
    "kpi": {
      "keyword": "e commerce kpi",
      "report_id": "pl1-commercial21",
      "group": "shop",
      "angle": "KPI e-commerce w GA4: jak ustalić plan pomiaru od produktu do zakupu przed wdrożeniem",
      "closest_existing_post_ids": [
        1828,
        3352,
        2539
      ],
      "distinctness_reason": "Zdarzenia i definicje liczników lejka sklepu, koszyk i zakup vs użytkownicy; nie ponowna konwersja formularza lub lista błędów koszyka.",
      "rank": 13,
      "resources": [
        {
          "title": "GA4: definicje metryk e-commerce",
          "url": "https://support.google.com/analytics/answer/13428834?hl=en"
        },
        {
          "title": "GA4: zbieranie zdarzeń e-commerce",
          "url": "https://developers.google.com/analytics/devguides/collection/ga4/ecommerce"
        }
      ],
      "source_ids": [
        "stat_kpi_1",
        "stat_kpi_2"
      ]
    },
    "omnichannel": {
      "keyword": "omnichannel",
      "report_id": "pl1-commercial21",
      "group": "shop",
      "angle": "Omnichannel w sklepie: jak ustalić obsługę zamówień, odbioru i zwrotów między kanałami",
      "closest_existing_post_ids": [
        5659,
        5798,
        5576
      ],
      "distinctness_reason": "Proces obsługi online i w punkcie, podział odpowiedzialności i testy odbioru; nie diagnostyka synchronizacji stanów.",
      "rank": 14,
      "resources": [
        {
          "title": "WooCommerce: zamówienia i normalny proces obsługi sklepu",
          "url": "https://woocommerce.com/document/managing-orders/"
        },
        {
          "title": "WooCommerce: statusy płatności, realizacji i zwrotu",
          "url": "https://woocommerce.com/document/managing-orders/order-statuses/"
        }
      ],
      "source_ids": [
        "stat_omnichannel_1",
        "stat_omnichannel_2"
      ]
    },
    "headless": {
      "keyword": "headless commerce",
      "report_id": "pl1-commercial21",
      "group": "shop",
      "angle": "Headless commerce a WooCommerce: kiedy oddzielny frontend uzasadnia dodatkowe utrzymanie",
      "closest_existing_post_ids": [
        5006,
        4936
      ],
      "distinctness_reason": "Architektura i odpowiedzialność za frontend, checkout i sesję; nie ponowny ranking platform.",
      "rank": 15,
      "resources": [
        {
          "title": "WooCommerce Store API: koszyk, checkout, sesja i ograniczenia",
          "url": "https://developer.woocommerce.com/docs/category/store-api"
        },
        {
          "title": "WooCommerce Checkout API: token, zamówienie i płatność",
          "url": "https://developer.woocommerce.com/docs/apis/store-api/resources-endpoints/checkout"
        }
      ],
      "source_ids": [
        "stat_headless_1",
        "stat_headless_2"
      ]
    },
    "web_push": {
      "keyword": "powiadomienia push",
      "report_id": "pl1-commercial21",
      "group": "web",
      "angle": "Powiadomienia push na własnej stronie: zgoda, przypadki użycia i plan testu",
      "closest_existing_post_ids": [
        2436,
        5710
      ],
      "distinctness_reason": "Web push i zarządzanie zgodą; nie ustawienia telefonu, SMTP ani remarketing Google Ads.",
      "rank": 16,
      "resources": [
        {
          "title": "MDN Push API: subskrypcja, service worker i bezpieczeństwo",
          "url": "https://developer.mozilla.org/en-US/docs/Web/API/Push_API"
        },
        {
          "title": "MDN Notifications API: zgoda użytkownika i ograniczenia przeglądarki",
          "url": "https://developer.mozilla.org/en-US/docs/Web/API/Notifications_API/Using_the_Notifications_API"
        }
      ],
      "source_ids": [
        "stat_web_push_1",
        "stat_web_push_2"
      ]
    }
  },
  "notes": [
    "Exact saved user-authorized native Senuto reports. No estimated volumes. Country1. Original first127 and second21 requests remain immutable.",
    "Positive and realzero rows from earlier cached pages outrank page-scoped synthetic missing placeholders.",
    "New16 is an editorial pool, not16 approved articles or promised clients. Source verification occurs in the existing native worker before writing.",
    "No new Senuto CREATE or replay. Private PHP artifact guards direct web access."
  ]
}
ZP_STATISTICS_FIXED_JSON
,true);
