# sfDoctrineRestGeneratorPlugin

## Introduction

This plugin permits to generate REST modules bound to Doctrine models. It
allows to easily create REST webservices, and provides an extensible framework
for data exchange. Here are some key features :

  * REST module generation "à la admin-generator"
  * easy-to-customize generator.yml configuration file
  * validation of the parameters passed to the service using symfony validators
  * serialization as XML, YAML or JSON feeds
  * possibility to embed related models, in retrieval as well as in creation requests
  * possibility to embed extra fields
  * ability to limit the number of results, with or without pagination
  * support for constraints unions (ie.,  http://api.example.org/city?city_id=12,13,14)
  * hookable through events and filters
  * abstract and replaceable objects serialization
  * full HTTP support (GET, POST, PUT, DELETE)


## How to install

  * go to your project's root

  * Install the plugin:

         ./symfony plugin:install http://plugins.symfony-project.com/sfDoctrineRestGeneratorPlugin


  * clear the cache:

         ./symfony cc


  * alternatively, you might prefer to install this plugin as a Subversion dependancy. In this case, here is the repository: [http://svn.symfony-project.com/plugins/sfDoctrineRestGeneratorPlugin](http://svn.symfony-project.com/plugins/sfDoctrineRestGeneratorPlugin). There is also a Git mirror on GitHub : [https://github.com/xavierlacot/sfDoctrineRestGeneratorPlugin](https://github.com/xavierlacot/sfDoctrineRestGeneratorPlugin)

## Usage

### REST module generation

Generating a REST module is pretty straightforward:

       ./symfony doctrine:generate-rest-module  APPLICATION MODULE MODEL


This will create a module named "MODULE" in the application "APPLICATION", and
this module will be configured to expose the "MODEL" model through a
REST-style service.


### What is generated

Let suppose we have the following model :

        Post:
          actAs:                      [ Timestampable ]
          columns:
            post_category_id:         integer(4)
            created_by:               integer
            title:                    {type: string(128), notnull: true}
            summary:                  {type: string(255), notnull: true}
            body:                     clob
          relations:
            CreatedBy:                { class: sfGuardUser, onDelete: SET NULL, local: created_by, foreign: id, foreignAlias: CreatedPost }
            PostCategory:             { class: PostCategory, onDelete: SET NULL, local: post_category_id, foreign: id }

        PostCategory:
          columns:
            id:                        { type: integer(4), primary: true, autoincrement: true }
            name:                      { type: string, size: 100, notnull: true, unique: true }
            description:               clob
            is_enabled:                { type: boolean, default: true }


If we want to expose the model "Post" through a REST API, we will simply type
the command:


        ./symfony doctrine:generate-rest-module  api post Post


This will generate:

 * a new route in the `routing.yml` file of the "api" application :

         post:
           class:   sfObjectRouteCollection
           options:
             model:   Post
             actions: [ create, delete, list, update ]
             module:  post
             column:  id
             format:  xml

 * a "post" RESTgen module, in apps/api/modules/post, with four sub-directories:
   * actions: contains a "postActions" class, which extends a on-the-fly generated "autopostActions" class,
   * config: contains the files generator.yml and view.yml (see the chapter "Service configuration" for explanations on how to configure the generated module),
   * lib: contains an empty "postGeneratorConfiguration" class, which extends a on-the-fly generated "BasePostGeneratorConfiguration" class,
   * templates

 * after a first request has been made to the REST module, the cache directory will contain the code of the generated module, and particularly the code of the "autopostActions" class, which you should check in order to understand the way the plugin works.


You should be able to see your posts as a JSON feed at http://api.example.com/post.json

If this is not the case, please check in the routing.yml file that the route
name comes with a minus "p". If you want to change the uri at which the API is
exposed, simply change the route name, and clear the cache of your project.
The following configuration will expose the API at
http://api.example.com/myPostAPI.json:

         myPostAPI:
           class:   sfObjectRouteCollection
           options:
             model:   Post
             actions: [ create, delete, list, update ]
             module:  post
             column:  id
             format:  xml



## Main configuration

Before configuring the content of the response of the webservice, the first
most important configuration steps must be undergone. They address some
general concerns which impact security, stability and define what the
webservice is supposed to do:

 * which types of operations does the webservice support? Only fetching objects, or also creating/updating/deleting it?
 * how does one access to the webservice? Which are the main security guidelines?

### Allowed operations

The REST web service supports four different operations:

 * getting a list of items ("list"): http://api.example.com/post.json (`GET` method),
 * creating a new item ("create"): http://api.example.com/post.json (`POST` method),
 * updating an existing item ("update"): http://api.example.com/post/123.json (`PUT` method, the value "123" must be replaced with the primary key of the object to update),
 * deleting an existing item ("delete"): http://api.example.com/post/123.json (`DELETE` method, the value "123" must be replaced with the primary key of the object to delete),

Each of these operations can be allowed or forbidden in the routing file,
with the `actions` key: only enable the ones that you want to use.

### Security guidelines

You should never forget that exposing a webservice with write access may harm
your data. Because of a miss of attention in the way your webservice is
secured, you could lose some important data, or have it be altered while it
shouldn't.

First, be sure to only allow the strictly required operations. If the client
do not have to delete items with the webservice, then disable this action in
the `routing.yml` file.

Second, consider a way to make your webservice more secure:

 * use SSL whenever possible, so that the posted data do not get intercepted and altered by a third party (man in the middle),
 * use HTTP authentication,
 * use a stronger / more extensible authentication system (OAuth for example),
 * deliver unique API keys to your clients, and check the usage that they do of the API.


## Detailed service configuration

As for symfony's admin-generator, the REST generator generates code on-the-fly,
depending on the configuration done in the `generator.yml` file.

Here is the default content of the `generator.yml` file:

        generator:
          class: sfDoctrineRestGenerator
          param:
            model_class:   Post

            config:
              default:
        #        fields:                                # list here the fields.
        #          created_at:                  { date_format: 'Y-m-d\TH:i:s', tag_name: 'created' }      # for instance
        #        formats_enabled:               [ json, xml, yaml ]    # enabled formats
        #        formats_strict:                true
        #        separator:                     ','     # separator used for multiple filters
              get:
        #        additional_params:             []      # list here additional params names, which are not object properties
        #        default_format:                json    # the default format of the response. If not set, will default to json. Accepted values are  "json", "xml" or "yaml"
        #        display:                       []      # list here the fields to render in the response
        #        embed_relations:               []      # list here relations to embed in the response
        #        embedded_relations_hide:
        #          Category:                    [id]    # you can hide fields inside a certain embedded relation
        #        global_additional_fields:      []      # list here additionnal calculated global fields
        #        hide:                          [id]    # list here the fields you don't want to expose
        #        max_items:                     0       # uncomment to fix an absolute limit to the number of items in the response
        #        object_additional_fields:      []      # list here additional calculated fields
        #        pagination_enabled:            false   # set to true to activate the pagination
        #        pagination_custom_page_size:   false   # set to true to allow the client to pass a page_size parameter
        #        pagination_page_size:          100     # the default number of items in a page
        #        sort_custom:                   false   # set to true to allow the client to pass a sort_by and a sort_order parameter
        #        sort_default:                  []      # set to [column, asc|desc] in order to sort on a column
        #        filters:                               # list here the filters
        #          created_at:                  { date_format: 'd-m-Y', multiple: true }  # for instance

The different possible parameters, commented in the previous sample, are
detailed in the following chapters.


### model_class

The `model_class` parameters defines the name of the Doctrine model the REST
module is bound to.

### default

The `default` option contains several general configuration directives:

#### fields

The `fields` option contains, for each of the fields of the model, an array of
decoration options that are used during the (de-)serialization. It might be:

  * `date_format`: the date format to use when formatting the field. This must be a format acceptable for the date() function,
  * `tag_name`: the tag name to use for displaying this field. For instance, you might want to associate the title of the post to the key "post_title", and not "title".


#### formats_enabled

This contains the list of the formats allowed in the communication with the
API. The default allowed formats are JSON, XML and YAML, JSON being the
default format.

This means that you can call a resource at the following URIs:

  * http://api.example.com/post will return a JSON formatted list of the posts,
  * http://api.example.com/post.xml will return a XML formatted list of the posts,
  * http://api.example.com/post.json will return a JSON formatted list of the posts.

Would you want to add a new serialization format, you should add this format
in the `generator.yml`, and create a serializer. See examples in the
`lib/serializer` directory of the plugin. You may also want to read the
section "How to create a new serialization format" below.


#### formats_strict

This indicates whether or not the api should return an error when an
un-handled format is requested or posted. If the option is activated (it is
by default), the module will display an error. If not, the module will use
the format by default.


#### separator

The separator to use in url when passing objects primary keys. The generated
module allows to require several resources identified by their ids:
http://api.example.com/post/?id=12,17,19


### get

The `get` option lists several options specific to the "get" operation:


#### additional_params

The `default_format` option allows to define an array of parameter names,
which the webservice will accept.

The validation of the parameters  in the generator is rather strict, and for
every unrecognised parameter passed to the service, the generator will launch
an exception. The option allows not to launch this exception for certain
parameter types, even if these parameters do not actually get used by the
generator.

The purpose of this parameter is to allow third-party params to be passed to
the service. For instance, you might want to pass a "`token`" or "`api_key`"
parameter, which could then be used to check if the client is allowed to use
the service.


#### default_format

The `default_format` option allows to define the default serialization format
when no format is asked for in the request. The accepted values are "json",
"xml" or "yaml", or any other serialization format that you could develop
(see the "Serialization" paragraph). If this parameter is not set, the
generator will default to a "json" serializer.


#### display

The `display` option contains the list of the fields to output in the XML or
JSON feed. For example with the previously defined "Post" model, you can
choose to only display the title and the author's id by changing this
parameter:

            config:
              get:
                display:                       [ title, author_id ]

If this option is left empty, all the fields of the model will be rendered.

You might also be interested in the `hide` option, which allows to hide some
fields of the model.


#### embed_relations

The `embed_relations` options contains the list of the Doctrine relations to
be embedded. It might be 1-n or n-n relations, which content will be embedded
in each object. Here is a valid configuration for our "Post" model:

            config:
              get:
                embed_relations:                       [ PostCategory ]

This configuration will produce a feed like:

    ...
    <Post>
      <Id>1</Id>
      <PostCategoryId>2</PostCategoryId>
      <CreatedBy>26</CreatedBy>
      <Title>Here the title of my post</Title>
      <Summary>Here the summary of my post</Summary>
      <Body>Here the body of my post</Body>
      <PostCategory>
        <Id>2</Id>
        <Name>Name of the category</Name>
        <Description>Description of the category</Description>
        <IsEnabled>1</IsEnabled>
      </PostCategory>
    </Post>
    ...

Several things to consider:

  * You cannot define the fields to render in the related objects, but you can hide certain with `embedded_relations_hide` option.
  * The response contains both the `PostCategoryId` field and the `PostCategory.Id` fields. You can save some bytes by using the `display` or the `hide` options, in order to remove the `PostCategoryId` field.


#### embedded_relations_hide

You may want to hide some fields from the embedded relations. For instance,
you could want to hide the `id` field from the PostCategory model. This can be
done using the `embedded_relations_hide` configuration option:

            config:
              get:
                embedded_relations_hide:
                  PostCategory:                     [id]


#### global_additional_fields

In some case, you might want to embed some additional fields in the XML or
JSON response. For instance, you might want to include the total number of
posts, an average price, etc.

The `global_additional_fields` is helpful in such a situation. It contains an
array of the fields that you want to add and, for each field, the generator
will create a method dedicated to embed this field. Here is a possible
configuration:

            config:
              get:
                global_additional_fields:                       [ TotalPosts ]

This will create an empty method, which has to be manually overridden in the
generated module, in order to include the additional field of your choice:

    public function embedGlobalAdditionalTotalPosts($params)
    {
      $totalObjects = count($this->objects);
      $this->objects['NbObjects'] = $totalObjects;
    }

#### hide

The `hide` option contains the list of the fields to hide in the XML or
JSON feed. For example with the previously defined "Post" model, you can
choose to hide its `id` by changing this parameter:

            config:
              get:
                hide:           [id]

This option has the priority over the "display" option, which means that if
both options are used, only the fields that are not listed in the "hide"
option will be rendered.


#### max_items

This directive allows to fix an absolute limit to the number of items in the
response. This parameter has the priority over the `pagination_page_size`
directive, and the possibly user's defined `page_size` parameter.

There is by default no limit. Setting this key to 0 will disable the limit.


#### object_additional_fields

The `object_additional_fields` contains the list of the additional fields
that have to be embedded in each item of the response. For instance, if you
want to add a field `NbWords`, which would give the number of words in the
body of the post, use the following configuration:

            config:
              get:
                object_additional_fields:                       [ NbWords ]

This will create an empty method, which has to be manually overridden in the
generated module, in order to include the additional field of your choice:

    public function embedAdditionalNbWords($item, $params)
    {
      $array = $this->objects[$item];
      $array['NbWords'] = str_word_count($array['body']);
      $this->objects[$item] = $array;
    }

This option is useful for embedding a relation with only a few fields (see
the `embed_relations` option):

 * embed the relation
 * use the `object_additional_fields` option to unset the non-desired fields.

The `embedAdditionalXXX()` methods should always have the following form (the
generator generates this code as comments):

    public function embedAdditionalXXXX($item, $params)
    {
      $array = $this->objects[$item];

      // here go some manipulation of $array

      $this->objects[$item] = $array;
    }


#### pagination_enabled

This option defines whether or not the pagination should be enabled. Defaults
to false. If enabled, the service will allow a parameter "page" to be passed
in the request. The request can then be of the form
http://api.example.org/post/?page=3

#### pagination_custom_page_size

Set this option to true to allow the client to pass a `page_size` parameter.
Else, the pagination will have a fixed size. If the `pagination_enabled`
option is set to false, this option will have no effect.

#### pagination_page_size

This option defile the default page size of the pagination. If the
`pagination_enabled` option is set to false, this option will have no effect.

#### sort_custom

Set this option to `true` to allow the client to pass a `sort_by` and a
`sort_order` parameter in query string. Else, the client will not be able to
sort the results.

#### sort_default

The `sort_default` option defines the default sort order. The format of this
option is [column, asc|desc]. For example:

            config:
              get:
                sort_default:                       [ created_at, desc ]


#### filters

This option allows to override the default filtering behavior by setting some
options. By default, the plugin allows to filter the results based on the
model's fields. For each field, it is possible to pass a value in query
string, which will be used to select the matching items.

For instance, you might want to get only the posts of a certain category
using a category_id parameter in the request. If you want to allow the client
to request the posts of several categories, you have to explicitly allow it,
as it may create more complex (ie. resource-consuming and slow) requests. In
that goal, the key `multiple` has to be set to `true` for this field name:

            config:
              get:
                filters:
                  category_id:                  { multiple: true }

This will allow to call the webservice with several category_ids at once, with
a request of the form http://api.example.org/post/?category_id=1,4,5

For the dates fields, you might want to tell the plugin which date format is
accepted. For example:

            config:
              get:
                filters:
                  created_at:                  { date_format: 'd-m-Y' }

### Other configuration variables

Some other configuration variables are not present in the default configuration file:

 * the `actions_base_class` parameter allows to change the name of the base action class which is extended by the module's action class. This permits to use your own action class, which may package several methods which you will want to use in several REST modules.



## Serialization

The response to a get request is formatted as a XML feed or a JSON array. The
XML serializer generates a valid feed, enclosing the content of a field in
CDATA sections if necessary.

The serialization is done directly in the action, not in the template, in
order to improve the performance when output escaping is enabled.


### How to create a new serialization format?

Creating a new serialization format, or overriding an existing serializer,
can simply be done by creating a "sfResourceSerializerXXX" class, where "XXX"
stands for the format you want to implement or override.

Here is for instance how the json serializer looks like:

      class sfResourceSerializerJson extends sfResourceSerializer
      {
        public function getContentType()
        {
          return 'application/json';
        }

        public function serialize($array, $rootNodeName = 'data')
        {
          return json_encode($array);
        }

        public function unserialize($payload)
        {
          return json_decode($array);
        }
      }

Would you like to add a "data" root node to the output json feed, you should
simply create a file in the `lib/serializers` directory of you project:

      class sfResourceSerializerJson extends sfResourceSerializer
      {
        public function getContentType()
        {
          return 'application/json';
        }

        public function serialize($array, $rootNodeName = 'data')
        {
          return json_encode(array($rootNodeName => $array));
        }

        public function unserialize($payload)
        {
          return json_decode(array_shift($payload), true);
        }
      }


## Events

As of version 0.9.1, the plugin uses events at several places, in order to
improve to overload and extend the default behavior.

Here is a list of the supported event names :

 * `sfDoctrineRestGenerator.filter_error_output`: This event is launched in order to filter the error message and enable its customisation
 * `sfDoctrineRestGenerator.filter_results`: This event is launched in order to filter the query result array (add, remove or changes some keys of it). Note that you can achieve the same thing by using the `object_additional_fields` parameter in the `generator.yml`
 * `sfDoctrineRestGenerator.filter_result`: This event is launched in order to filter a single query result (add, remove or changes some keys of it). Note that you can achieve the same thing by using the `object_additional_fields` parameter in the `generator.yml`
 * `sfDoctrineRestGenerator.get.pre`: This event gets fired at the very beginning of a request (at the beginning of `executeIndex()`)


## Whishlist

If you use the plugin and want to help me improve it, you could consider
picking one of the following topics:

 * possibility to nest the embed_relations parameter (and not limit it at one level only)
 * possibility to disable events notification / filtering (performance)
 * more serializers ([BSON](http://bsonspec.org/) or RDF for example). Currently, the plugin only allows to serialize the resultsets as a XML, YAML or JSON feeds (see the chapter "Serialization"). Mobile clients, which require the most compact possible streams, would take benefit from a BSON serialization.
 * possibility to generate client libraries (sfDoctrineRestClientGenerator ?)
 * possibility to generate unit tests
 * possibility to generate API documentation
 * document authentication solutions
 * all the possible feedback!


## Contribute to the plugin, ask for help

Please ask for help on how to use the plugin on symfony's users mailing list.
You can also send me a mail directly : xavier@lacot.org.


## License and credits

This plugin has been developed by [Xavier Lacot](http://lacot.org/) and is
licensed under the MIT license.


## Changelog

### version 0.9.4 - 2010-11-25

 * Allow to create or update related objects in the very same request: payload parsing in a recursive way, recursive validation of the payload array, recursive generation of the validators array. Thanks to dfeyer for his contributions on this topic
 * Added a basic "show" action (thanks dfeyer)
 * Improved the performance of the XML serializer
 * Added the capacity to serialize single objects (thanks dfeyer)
 * Added cleanupParameters() method to avoid code duplication (thanks dfeyer)
 * Added setFieldVisibility() method, in order to share code between index and show actions (thanks dfeyer)
 * Added configureFields() method, in order to share code between index and show actions (thanks dfeyer)
 * Fixed executeUpdate() so that the content is not a parameter of the request, but the content of the PUT request (thanks dfeyer)
 * Fix the setFieldVisibility() method, so that object additional fields don't get stripped if they're not listed in the 'get.display' option
 * Have the XML serializer return a PHP array (thanks dfeyer)
 * Added the `formats_strict` option
 * Improved the deserialization abstraction
 * Added a YAML serializer
 * Improved the documentation on these topics


### version 0.9.3 - 2010-08-13

 * Added `hide` and `embedded_relations_hide` options (thanks to Pascal Borreli)
 * Made the fields filtering faster


### version 0.9.2 - 2010-07-01

 * Added events (thanks to Matthew Penrice)
 * Added a default_format parameter (thanks to Matthew Penrice)
 * Added PUT support
 * Switched to json as default serializer (faster and less verbose)
 * Improved the documentation


### version 0.9.1 - 2010-05-14

 * Fixed a typo in the previous release


### version 0.9 - 2010-05-14

 * Added a JSON serializer.


### version 0.8 - 2010-05-09

 * Initial public release. Features REST module generation with validation and a XML serializer.