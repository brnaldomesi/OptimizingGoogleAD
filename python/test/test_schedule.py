# # coding: utf-8
# import unittest

# from schedule.Schedule import Schedule

# class TestSchedule(unittest.TestCase):

#     def __init__(self, *args, **kwargs):
#         super(TestSchedule, self).__init__(*args, **kwargs)

#     def test_get_synced_accounts(self):
#         """There should always be at least one synced account returned"""
#         self.assertTrue(len(Schedule().getSyncedAccountIds())>0)

#     def test_account_should_run(self):
#         """Pass an id which should run on the schedule. Should return true"""
#         account_id = '0bafd106-696d-4bba-93a9-9e42ff62f55f'
#         self.assertTrue(Schedule().accountShouldRun(account_id))

#     def test_account_should_run_run_all(self):
#         """Pass an id which should NOT run on the schedule. Should return true because of the RUN_ALL_ACTIVE_ACCOUNTS env
#         Set RUN_ALL_ACTIVE_ACCOUNTS to true """
#         if not Schedule().run_all_active_accounts:
#             print('RUN_ALL_ACTIVE_ACCOUNTS needs to be true for this test')
#             return
#         account_id = '497d2852-2a23-4b82-904f-c871bec0f062'
#         self.assertTrue(Schedule().accountShouldRun(account_id))

#     def test_account_should_not_run(self):
#         """Pass an id which should NOT run on the schedule. Should return false"""
#         if Schedule().run_all_active_accounts:
#             return
#         account_id = '497d2852-2a23-4b82-904f-c871bec0f062'
#         self.assertFalse(Schedule().accountShouldRun(account_id))

