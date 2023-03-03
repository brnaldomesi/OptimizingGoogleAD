# import unittest

# from api.mutations_queue_workers.Operation import Operation


# class OperationTestCase(unittest.TestCase):

#     def setUp(self):
#         self.operation = Operation(
#             row={'entity_google_id': '1231,9939', 'action': 'update', 'value': 'enabled'},
#             service_type='keyword',
#             action='set',
#             attribute='status',
#         )

#         self.operation_batch_processing = Operation(
#             row={'entity_google_id': '1231,9939', 'action': 'update', 'value': 'enabled'},
#             service_type='keyword',
#             action='set',
#             attribute='status',
#         )

#     def test_getKeywordStatusOperation(self):
#         result = self.operation.get()

#         self.assertEqual(result['operator'], 'UPDATE')

#     def test_getKeywordBidOperation(self):
#         self.operation.attribute = 'bid'
#         self.operation.row['value'] = 100

#         result = self.operation.get()

#         self.assertEqual(result['operator'], 'UPDATE')

#     def test_getAdvertOperation(self):
#         self.operation.service_type = 'advert'
#         self.operation.row['attribute'] = 'status'

#         result = self.operation.get()

#         self.assertEqual(result['operand']['status'], 'ENABLED')

#     def test_getAdvertCreationOperation(self):
#         self.operation.service_type = 'campaign'
#         self.operation.row['attribute'] = 'status'
#         self.operation.row['entity_google_id'] = '1234'

#         result = self.operation.get()

#         self.assertEqual(result['operand']['status'], 'ENABLED')
#         self.assertEqual(result['operand']['id'], '1234')

#     def test_getCampaignStatusOperation(self):
#         self.operation.service_type = 'campaign'
#         self.operation.row['attribute'] = 'status'
#         self.operation.row['entity_google_id'] = '3333'

#         result = self.operation.get()

#         self.assertEqual(result['operand']['status'], 'ENABLED')
#         self.assertEqual(result['operand']['id'], '3333')

#     def test_getKeywordStatusOperation_batch_processing(self):
#         result = self.operation_batch_processing.get()

#         self.assertEqual(result['xsi_type'], 'AdGroupCriterionOperation')
#         self.assertEqual(len(result), 3)

#     # TODO: pending test
#     def test_adTextToDict(self):
#         pass

#     def test_split_entity_google_id(self):
#         result = self.operation.split_entity_google_id(
#             '4444,56665'
#         )

#         self.assertEqual(result[1], '4444')
