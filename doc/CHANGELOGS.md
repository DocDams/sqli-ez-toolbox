CHANGELOGS
----------
### Differences between 1.x and 2.0
- Compatibility with Symfony 5 and Twig 3.0
- Compatibility with Ibexa 3.3 (OSS/Content/Experience/Commerce)
- Requires [Netgen Tagsbundle](https://github.com/netgen/TagsBundle) 3.4 to 4.0
- Removal of SQLIContentTypeInstaller
- Adding suggest of [MigrationBundle](https://github.com/tanoconsulting/ezmigrationbundle2) 
  in replacement of SQLIContentTypeInstaller
- PSR12
- Replacement of ContainerInterface by ParameterBagInterface in EntityExtension
- [[PR#2]](https://github.com/DocDams/sqli-eztoolbox/pull/2/commits) Use the CacheItemPoolInterface in CleanRedisCacheCommand instead to get the service from Container